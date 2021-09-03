<?php
namespace Modules\Sms\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * @author liming
 * @date 2021-07-02 10:50
 */
class SmsGatewaysSeeder extends Seeder
{
    public function run()
    {
        if (Schema::hasTable('sms_gateways')){
            $info = DB::table('sms_gateways')->where('id', '>', 0)->first();
            if(!$info){
                $arr = $this->defaultInfo();
                if(!empty($arr) && is_array($arr)) {
                    $created_at = $updated_at = date("Y-m-d H:i:s");
                    foreach ($arr as $name => $item) {
                        DB::table('sms_gateways')->insert([
                            'code' => $name,
                            'params' => serialize($item['params']),
                            'param_view' => $item['param_view'] ?? 'gateway_params_common',
                            'is_default' => 0,
                            'remark' => $item["remark"] ?? "",
                            'url' => $item["url"] ?? "",
                            'created_at' => $created_at,
                            'updated_at' => $updated_at,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * 新增短信服务商信息
     * 短信服务商只配置了 短信内容为 template + data 的方式的，其它的暂时没有配置
     */
    private function defaultInfo()
    {
        return [
            'aliyun' => [
                "params" => ['access_key_id' => '', 'access_key_secret' => '', 'sign_name' => ''],
                "param_view" => "gateway_params_common",
                "remark" => "阿里云",
                "url" => "https://www.aliyun.com/",
            ],
            'aliyunrest' => [
                "params" => ['app_key' => '', 'app_secret_key' => '', 'sign_name' => ''],
                "param_view" => "gateway_params_common",
                "remark" => "阿里云Rest",
                "url" => "https://www.aliyun.com/",
            ],
            'yuntongxun' => [
                "params" => ['app_id' => '', 'account_sid' => '', 'account_token' => '', 'is_sub_account' => false],
                "param_view" => "gateway_params_common",
                "remark" => "容联云通讯",
                "url" => "https://www.yuntongxun.com/",
            ],
            'juhe' => [
                "params" => ['app_key' => ''],
                "param_view" => "gateway_params_common",
                "remark" => "聚合数据",
                "url" => "https://www.juhe.cn/",
            ],
            'sendcloud' => [
                "params" => ['sms_user' => '', 'sms_key' => '', 'timestamp' => false], // timestamp 是否启用时间戳
                "param_view" => "gateway_params_common",
                "remark" => "SendCloud",
                "url" => "https://www.sendcloud.net/v3/#/home",
            ],
            'baidu' => [
                "params" => ['ak' => '', 'sk' => '', 'invoke_id' => '', 'domain' => ''],
                "param_view" => "gateway_params_common",
                "remark" => "百度云",
                "url" => "https://cloud.baidu.com/",
            ],
            'rongcloud' => [
                "params" => ['app_key' => '', 'app_secret' => ''],
                "param_view" => "gateway_params_common",
                "remark" => "融云",
                "url" => "https://www.rongcloud.cn/",
            ],
            'avatardata' => [
                "params" => ['app_key' => ''],
                "param_view" => "gateway_params_common",
                "remark" => "阿凡达数据",
                "url" => "http://www.avatardata.cn/",
            ],
            'huawei' => [
                // from: 通道数组(default=默认通道)  callback: 短信状态回调地址
                "params" => ['endpoint' => '', 'app_key' => '', 'app_secret' => '', 'from' => [
                    'default' => '',
                ], 'callback' => ''],
                "param_view" => "gateway_params_huawei",
                "remark" => "华为云 SMS",
                "url" => "https://www.huaweicloud.com/product/msgsms.html",
            ],
            'yunxin' => [
                // code_length: 随机验证码长度，范围 4～10，默认为 4     need_up: 是否需要支持短信上行
                "params" => ['app_key' => '', 'app_secret' => '', 'code_length' => 4, 'need_up' => false],
                "param_view" => "gateway_params_common",
                "remark" => "网易云信",
                "url" => "https://yunxin.163.com/sms",
            ],
            'yunzhixun' => [
                "params" => ['sid' => '', 'token' => '', 'app_id' => ''],
                "param_view" => "gateway_params_common",
                "remark" => "云之讯",
                "url" => "https://www.ucpaas.com/index.html",
            ],
            'qiniu' => [
                "params" => ['secret_key' => '', 'access_key' => ''],
                "param_view" => "gateway_params_common",
                "remark" => "七牛云",
                "url" => "https://www.qiniu.com/",
            ],
            'ucloud' => [
                // project_id：项目ID,子账号才需要该参数
                "params" => ['private_key' => '', 'public_key' => '', 'sig_content'  => '', 'project_id' => ''],
                "param_view" => "gateway_params_common",
                "remark" => "Ucloud",
                "url" => "https://www.ucloud.cn/",
            ],
            'moduyun' => [
                "params" => ['accesskey' => '', 'secretkey' => '', 'signId'  => '', 'type' => 0],
                "param_view" => "gateway_params_common",
                "remark" => "摩杜云",
                "url" => "https://www.moduyun.com/",
            ],
            'rongheyun' => [
                "params" => ['username' => '', 'password' => '', 'signature'  => ''],
                "param_view" => "gateway_params_common",
                "remark" => "融合云（助通）",
                "url" => "https://www.ztinfo.cn/products/sms",
            ],
            'zzyun' => [
                "params" => ['user_id' => '', 'secret' => '', 'sign_name'  => ''],
                "param_view" => "gateway_params_common",
                "remark" => "蜘蛛云",
                "url" => "https://zzyun.com/",
            ]
        ];
    }
}