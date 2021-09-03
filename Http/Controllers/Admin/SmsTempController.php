<?php
// @author liming
namespace Modules\Sms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\Sms\Http\Controllers\Controller;
use Modules\Sms\Http\Requests\Admin\SmsTempEditRequest;
use Modules\Sms\Entities\SmsGateway;
use Modules\Sms\Entities\SmsTemp;
use Modules\Sms\Entities\SmsVariable;

class SmsTempController extends Controller
{
    /**
     * 短信模板分页列表
     * @date 2021-06-25 17:58
     */
    public function list()
    {
        $statusArr = SmsTemp::getStatusArr();
        $gatewayArr = SmsGateway::all();
        return view('smsconfig::admin.sms_temp.list', compact("statusArr", "gatewayArr"));
    }

    /**
     * ajax获取列表数据
     */
    public function ajaxList(Request $request)
    {
        $pagesize = $request->input('limit'); // 每页条数
        $page = $request->input('page',1);//当前页
        $where = [];
        $status = $request->input('status');
        if($status != "") $where[] = ["status", "=", $status];

        $temp_name = $request->input('temp_name');
        if($temp_name != "") $where[] = ["temp_name", "like", "%{$temp_name}%"];

        $gateway_id = $request->input("gateway_id");
        if($gateway_id != "") $where[] = ["gateway_id", "like", "%{$gateway_id}%"];

        //获取总条数
        $count = SmsTemp::where($where)->count();

        //求偏移量
        $offset = ($page-1)*$pagesize;
        $list = SmsTemp::where($where)->offset($offset)->limit($pagesize)->orderBy("id", "desc")->get();
        foreach ($list as &$item){
            $item->gateway;
            $item->gateway_name = $item->gateway->remark ?? "";
            $item->status_name = $item->status_name;
        }
        return $this->success(compact('list', 'count'));
    }

    /**
     * 新增|编辑服务商信息
     * @param $id
     */
    public function edit(SmsTempEditRequest $request)
    {
        if($request->isMethod('post')) {
            $request->check();
            $data = $request->post();

            if(isset($data["id"])){
                $info = SmsTemp::where("id",$data["id"])->first();
                if(!$info) return $this->failed('数据不存在');
            }else{
                $info = new SmsTemp();
            }

            $gatewayInfo = SmsGateway::where("id",$data["gateway_id"])->first();
            if(!$gatewayInfo) return $this->failed('服务商不存在');

            $info->gateway_id = $data["gateway_id"];
            $info->temp_name = $data["temp_name"];
            $info->temp_key = $gatewayInfo->code . "_" . $data["temp_code"];
            $info->temp_code = $data["temp_code"];
            $info->content = $data["content"];
            $info->variable = [];
            if(isset($data["variable"]) && is_array($data["variable"])){
                foreach ($data["variable"] as $k => $variable){
                    $ii = SmsVariable::where("key", $variable)->first();
                    if(!$ii) return $this->failed("模板变量不存在");
                }
                $info->variable = $data["variable"];
            }
            $info->variable = json_encode($info->variable, JSON_UNESCAPED_UNICODE);
            $info->status = $data["status"] ?? 1;
            if(!$info->save()) return $this->failed("操作失败");
            return $this->success();
        } else {
            $id = $request->input('id') ?? 0;
            if($id > 0){
                $info = SmsTemp::where('id',$id)->first();
                $title = "编辑模板";
                $info->variable = json_decode($info->variable, JSON_UNESCAPED_UNICODE);
            }else{
                $info=(object)[];
                $title = "新增模板";
            }
            $gatewayList = SmsGateway::all();
            $variableList = SmsVariable::all();
            return view('smsconfig::admin.sms_temp.edit', compact('info', 'title', 'gatewayList', 'variableList'));
        }
    }

    /**
     * 删除模板
     */
    public function del(Request $request)
    {
        if($request->isMethod('post')){
            $id = $request->input('id');
            $info = SmsTemp::where('id',$id)->first();
            if(!$info) return $this->failed("数据不存在");
            if($info->delete()){
                return $this->success();
            }else{
                return $this->failed('操作失败');
            }
        }
        return $this->failed('请求出错.');
    }

}
