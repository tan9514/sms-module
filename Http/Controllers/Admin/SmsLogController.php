<?php

namespace Modules\Sms\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Modules\Sms\Http\Controllers\Controller;
use Modules\Sms\Entities\SmsGateway;
use Modules\Sms\Entities\SmsLog;

class SmsLogController extends Controller
{
    /**
     * 短信发送记录日志分页列表
     * @author liming
     * @date 2021-06-25 17:58
     */
    public function list()
    {
        $statusArr = SmsLog::getStatusArr();
        $gatewayArr = SmsGateway::all();
        return view('smsconfig::admin.sms_log.list', compact('gatewayArr', 'statusArr'));
    }

    /**
     * ajax获取列表数据
     */
    public function ajaxList(Request $request)
    {
        $pagesize = $request->input('limit'); // 每页条数
        $page = $request->input('page',1);//当前页
        $where = [];
        $phone = $request->input('phone');
        if($phone != "") $where[] = ["phone", "=", $phone];

        $status = $request->input('status');
        if($status != "") $where[] = ["status", "=", $status];

        $gateway_id = $request->input("gateway_id");
        if($gateway_id != "") $where[] = ["gateway_id", "like", "%{$gateway_id}%"];

        //获取总条数
        $count = SmsLog::where($where)->count();

        //求偏移量
        $offset = ($page-1)*$pagesize;
        $list = SmsLog::where($where)->offset($offset)->limit($pagesize)->orderBy("id", "desc")->get();
        foreach ($list as &$item){
            $item->phone = substr($item->phone, 0, 3) . '****' . substr($item->phone, 7);
            $item->gateway;
            $item->gateway_name = $item->gateway->remark ?? "";
            $item->status_name = $item->status_name;
        }
        return $this->success(compact('list', 'count'));
    }

    /**
     * 删除记录
     * @param Request $request
     * @return mixed
     */
    public function del(Request $request)
    {
        if($request->isMethod('post')){
            $ids = $request->input('ids');
            if(is_array($ids) && empty($ids)){
                return $this->failed("请选择要删除的数据");
            }else if(is_array($ids) && !empty($ids)){
                if(SmsLog::whereIn("id", $ids)->delete()){
                    return $this->success();
                }else{
                    return $this->failed('操作失败');
                }
            }else if($ids > 0){
                $info = SmsLog::where("id", $ids)->first();
                if(!$info) return $this->failed("数据不存在");
                if($info->delete()){
                    return $this->success();
                }else{
                    return $this->failed('操作失败');
                }
            }else{
                return $this->failed("请选择要删除的数据");
            }
        }
        return $this->failed('请求出错.');
    }
}
