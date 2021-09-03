<?php
// @author liming
namespace Modules\Sms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Sms\Http\Controllers\Controller;
use Modules\Sms\Entities\SmsGateway;

class SmsGatewayController extends Controller
{
    /**
     * 短信服务商分页列表
     * @date 2021-06-25 17:58
     */
    public function list()
    {
        return view('smsview::admin.sms_gateway.list');
    }

    /**
     * ajax获取列表数据
     */
    public function ajaxList(Request $request)
    {
        $pagesize = $request->input('limit'); // 每页条数
        $page = $request->input('page',1);//当前页
        $where = [];
        $is_default = $request->input('is_default');
        if($is_default != "") $where["is_default"] = $is_default;
        //获取总条数
        $count = SmsGateway::where($where)->count();

        //求偏移量
        $offset = ($page-1)*$pagesize;
        $list = SmsGateway::where($where)->offset($offset)->limit($pagesize)->get();
        return $this->success(compact('list', 'count'));
    }

    /**
     * 修改默认网关状态
     */
    public function saveDefault(Request $request)
    {
        if($request->isMethod('post')){
            $status = $request->input('is_default');
            $id = $request->input('id');
            if($status == 'true'){
                //启用
                $info = SmsGateway::checkStatus($id);
                if(isset($info["msg"])) return $this->failed($info["msg"]);
                $reulst = SmsGateway::where('id',$id)->update(['is_default'=>1]);
            }else{
                //禁用
                $reulst = SmsGateway::where('id',$id)->update(['is_default'=>0]);
            }
            if($reulst !== false){
                return $this->success();
            }else{
                return $this->failed('操作失败');
            }
        }
        return $this->failed('请求出错.');
    }

    /**
     * 修改服务商信息
     * @param $id
     */
    public function edit(Request $request)
    {
        if($request->isMethod('post')) {
            $data = $request->all();
            $id = $data["id"] ?? 0;
            $info = SmsGateway::find($id);
            if(!$info) return $this->failed('数据不存在');

            // 处理参数
            $params = $data["params"] ?? [];
            if(!is_array($params)) return $this->failed('非法操作');
            if(empty($params)) return $this->failed('非法操作');
            $boolArr = ["true", "false"];
            foreach ($params as &$item){
                if(in_array($item, $boolArr)){  // 判断是否是 true 与 false
                    $item = $item === "true" ? true : false;
                }else{  // 正常字符串参数
                    $item = $item ?? "";
                }
            }

            // 华为云多签名处理
            $form = $data["from"] ?? [];
            if(is_array($form) && !empty($form)
                && isset($form["code"]) && is_array($form["code"])
                && isset($form["value"]) && is_array($form["value"])){
                $newForm = [];
                $codeArr = [];
                foreach ($form["code"] as $k => $item){
                    if($item != ""  && !in_array($item, $codeArr)){
                        $newForm[$item] = $form["value"][$k] ?? "";
                    }
                }
                // 重新排序
                $newParams = [];
                foreach ($params as $kk => $ii){
                    if($kk === "callback"){
                        $newParams["from"] = $newForm;
                    }
                    $newParams[$kk] = $ii;
                }
                $params = $newParams;
            }

            // 编辑信息
            $reulst = SmsGateway::where('id',$id)->update([
                "params" => serialize($params),
            ]);
            if(!$reulst) $this->failed('操作失败');
            return $this->success();
        } else {
            $id = $request->input('id');
            $info = SmsGateway::where('id',$id)->first();
            if($info) $info->params = unserialize($info->params);
            return view('smsview::admin.sms_gateway.edit', compact('info'));
        }
    }

}
