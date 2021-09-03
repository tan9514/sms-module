<?php

namespace Modules\Sms\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Sms\Http\Controllers\Controller;
use Modules\Sms\Http\Requests\Api\CheckCodeRequest;
use Modules\Sms\Http\Requests\Api\SendSmsRequest;
use Modules\Sms\Entities\SmsGateway;
use Modules\Sms\Entities\SmsLog;
use Modules\Sms\Entities\SmsTemp;
use Overtrue\EasySms\EasySms;

class SmsController extends Controller
{
    /**
     * 请求发送短信
     */
    public function send(SendSmsRequest $request)
    {
        $request->check();
        $temp_key = $request->input("temp_key");
        $phone = $request->input("phone");

        // 查询短信模板
        $temp = SmsTemp::where([
            ["temp_key", "=", $temp_key],
            ["status", "=", 2],
        ])->first();
        if(!$temp) return $this->failed("短信模板不存在",  config('smscode.error'));
        $temp->variable = json_decode($temp->variable, true);

        // 获取发送短信配置
        $config = config('smseasysms');
        // 获取可用的服务商网关
        $gateways = SmsGateway::where([
            ["id", "=", $temp->gateway_id],
            ["is_default", "=", 1],
        ])->get();
        if(count($gateways) <= 0) return $this->failed("没有可用的短信服务商", config('smscode.error'));

        // 获取短信配置
        $smsSetting = config('smscode.setting');
        // todo::短信设置有问题 等待后面修改
//        $plugin_type = config('smsconfig.module') ?? "";
//        if($plugin_type != ""){
//            $smsConfigSetting = Setting::where([
//                ["plugin_type", "=", $plugin_type],
//            ])->get();
//            if(count($smsConfigSetting) > 0){
//                foreach ($smsConfigSetting as &$settingInfo){
//                    $settingInfo->content = json_decode($settingInfo->content, true);
//                    $smsSetting[$settingInfo->code] = $settingInfo->content["value"];
//                }
//            }
//        }

        // 判断配置
        foreach ($smsSetting as $setKey => $setVal){
            switch ($setKey){
                case "is_open": // 判断是否开启
                    if($setVal != 1) return $this->failed("短信服务没有开启", config('smscode.error'));
                    break;
                case "interval": // 发送间隔分钟
                    $lastLog = SmsLog::where([
                        ["phone", "=", $phone],
                    ])->whereIn("status", [1,2])->orderBy("created_at", "desc")->first();
                    if($lastLog){
                        $createdTime = strtotime($lastLog->created_at);
                        $ss = $setVal * 60;
                        $newTime = time() - $ss;
                        if($newTime <= $createdTime) return $this->failed("不要频繁的请求发送短信，请间隔{$setVal}分钟后再试", config('smscode.error'));
                    }
                    break;
                case "minute_max": // 同手机号每分钟最大数量
                    $time = time();
                    $startTime = $time - 60;
                    $logCount = SmsLog::where([
                        ["phone", "=", $phone],
                        ["created_at", ">=", date("Y-m-d H:i:s", $startTime)],
                        ["created_at", "<=", date("Y-m-d H:i:s", $time)],
                    ])->whereIn("status", [1,2])->count();
                    if($logCount >= $setVal) return $this->failed("您请求短信发送次数已达到每分钟上限，请稍后再试", config('smscode.error'));
                    break;
                case "hous_max": // 同手机号每小时最大数量
                    $time = time();
                    $ss = 60 * 60;
                    $startTime = $time - $ss;
                    $logCount = SmsLog::where([
                        ["phone", "=", $phone],
                        ["created_at", ">=", date("Y-m-d H:i:s", $startTime)],
                        ["created_at", "<=", date("Y-m-d H:i:s", $time)],
                    ])->whereIn("status", [1,2])->count();
                    if($logCount >= $setVal) return $this->failed("您请求短信发送次数已达到每小时上限，请稍后再试", config('smscode.error'));
                    break;
                case "day_max": // 同手机号每天最大数量
                    $time = time();
                    $startTime = strtotime(date("Y-m-d", $time) . " 00:00:00");
                    $logCount = SmsLog::where([
                        ["phone", "=", $phone],
                        ["created_at", ">=", date("Y-m-d H:i:s", $startTime)],
                        ["created_at", "<=", date("Y-m-d H:i:s", $time)],
                    ])->whereIn("status", [1,2])->count();
                    if($logCount >= $setVal) return $this->failed("您请求短信发送次数已达到每天上限，请明天再试", config('smscode.error'));
                    break;
            }
        }

        // 重组服务商数组
        $newDefaultGateways = [];
        $newGateways = [];
        $newGatewayIds = [];
        foreach ($gateways as $item){
            $newDefaultGateways[] = $item->code;
            $newGateways[$item->code] = unserialize($item->params);
            $newGatewayIds[$item->code] = $item->id;
        }

        // 重组配置
        $config["default"]["gateways"] = $newDefaultGateways;
        $config["gateways"] = array_merge($config["gateways"], $newGateways);

        // 重组内容
        $content = $temp->content;
        $variableData = [];
        if(!empty($temp->variable)){
            foreach ($temp->variable as $variable){
                switch ($variable){
                    case "code":  // 验证码
                        mt_srand(time());
                        $variableData["code"] = mt_rand(100000,999999);
                        break;
                    case "orderno": // 订单编号
                        break;
                    case "nickname": // 会员昵称
                }
            }
        }
        $sendParamData = [
            "content" => $content,
            "template" => $temp->temp_code,
            "data" => $variableData,
        ];

        // 新增发送记录
        $log = new SmsLog();
        $log->gateway_id = $temp->gateway_id;
        $log->phone = $phone;
        $log->sms_code = (isset($variableData["code"]) && $variableData["code"] != "") ? $variableData["code"] : "";
        $log->send_params = json_encode($sendParamData, JSON_UNESCAPED_UNICODE);
        $log->result_params = json_encode([], JSON_UNESCAPED_UNICODE);
        if(!$log->save()) return $this->failed("发送失败：新增发送短信记录失败", config('smscode.error'));

        // 发送短信
        try {
            $easySms = new EasySms($config);
            $res = $easySms->send($phone, $sendParamData);

            // 编辑发送记录
            $log->result_params = json_encode($res, JSON_UNESCAPED_UNICODE);
            $log->status = 2;
            $log->save();

            return $this->success([],"发送成功");
        }catch (\Exception $e){
            $ee = $e->getExceptions();
            $log->result_params = json_encode($ee, JSON_UNESCAPED_UNICODE);
            $log->status = 3;
            $log->save();
            return $this->failed("发送失败", config('smscode.error'));
        }
    }

    /**
     * 请求验证短信验证码正确性
     * @param CheckCodeRequest $request
     * @return mixed
     */
    public function checkcode(CheckCodeRequest $request)
    {
        $request->check();
        $code = $request->input("code");
        $phone = $request->input("phone");

        // 查询信息
        $time = time();
        $ss = 60 * 5;
        $startTime = $time - $ss;
        $logInfo = SmsLog::where([
            ["phone", "=", $phone],
            ["status", "=", 2],
            ["sms_code", "=", $code],
            ["created_at", ">=", date("Y-m-d H:i:s", $startTime)],
            ["created_at", "<=", date("Y-m-d H:i:s", $time)],
        ])->first();
        if(!$logInfo) return $this->failed("验证码错误或者验证码已过期，请重新输入", config('smscode.error'));
        return $this->success();
    }
}
