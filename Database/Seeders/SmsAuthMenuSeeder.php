<?php
namespace Modules\Sms\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * @author liming
 * @date 2021-07-02 10:50
 */
class SmsAuthMenuSeeder extends Seeder
{
    public function run()
    {
        if (Schema::hasTable('auth_menu')){
            $arr = $this->defaultInfo();
            if(!empty($arr) && is_array($arr)) {
                // 删除原来已存在的菜单
                $module = config('smsconfig.module') ?? "";
                if($module != ""){
                    DB::table('auth_menu')->where("module", $module)->delete();
                }

                $this->addInfo($arr);
            }
        }
    }

    /**
     * 遍历新增菜单
     * @param array $data
     * @param int $pid
     */
    private function addInfo(array $data, $pid = 0)
    {
        foreach ($data as $item) {
            $newPid = DB::table('auth_menu')->insertGetId([
                'pid' => $item['pid'] ?? $pid,
                'href' => $item['href'],
                'title' => $item['title'],
                'icon' => $item['icon'],
                'type' => $item['type'],
                'status' => $item['status'],
                'sort' => $item['sort'] ?? 0,
                'remark' => $item['remark'],
                'target' => $item['target'],
                'createtime' => $item['createtime'],
                'module' => $item["module"],
                'menus' => $item["menus"],
            ]);
            if($newPid <= 0) break;
            if(isset($item["contents"]) && is_array($item["contents"]) && !empty($item["contents"])) $this->addInfo($item["contents"], $newPid);
        }
    }

    /**
     * 设置后台管理菜单路由信息
     * @pid 父级
     * @href 路由
     * @title 菜单标题
     * @icon 图标
     * @type 类型 0 顶级目录 1 目录 2 菜单 3 按钮
     * @status 状态 1 正常 2 停用
     * @remark 备注
     * @target 跳转方式
     * @createtime 创建时间
     */
    private function defaultInfo()
    {
        $module = config('smsconfig.module') ?? "";
        $time = time();
        return [
            [
                "pid" => 10002,
                "href" => "",
                "title" => "短信管理",
                "icon" => 'fa fa-envelope',
                "type" => 1,
                "status" => 1,
                "sort" => 70,
                "remark" => "短信管理",
                "target" => "_self",
                "createtime" => $time,
                'module' => $module,
                "menus" => $module == "" ? $module : $module . "-1",
                "contents" => [
                    [   //  发送记录
                        "href" => "/admin/sms_log/list",
                        "title" => "发送记录",
                        "icon" => 'fa fa-file-text-o',
                        "type" => 2,
                        "status" => 1,
                        "remark" => "发送记录",
                        "target" => "_self",
                        "createtime" => $time,
                        'module' => $module,
                        "menus" => $module == "" ? $module : $module . "-2",
                        "contents" => [
                            [
                                "href" => "/admin/sms_log/list",
                                "title" => "查看发送记录",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "查看发送记录",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-3",
                            ],
                            [
                                "href" => "/admin/sms_log/ajaxList",
                                "title" => "异步获取发送记录信息",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "异步获取发送记录信息",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-4",
                            ],
                            [
                                "href" => "/admin/sms_log/del",
                                "title" => "删除记录",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "删除记录",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-5",
                            ],
                        ],
                    ],
                    [   //  服务商
                        "href" => "/admin/sms_gateway/list",
                        "title" => "服务商",
                        "icon" => 'fa fa-file-text-o',
                        "type" => 2,
                        "status" => 1,
                        "remark" => "服务商",
                        "target" => "_self",
                        "createtime" => $time,
                        'module' => $module,
                        "menus" => $module == "" ? $module : $module . "-6",
                        "contents" => [
                            [
                                "href" => "/admin/sms_gateway/list",
                                "title" => "查看服务商",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "查看服务商",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-7",
                            ],
                            [
                                "href" => "/admin/sms_gateway/ajaxList",
                                "title" => "异步获取服务商信息",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "异步获取服务商信息",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-8",
                            ],
                            [
                                "href" => "/admin/sms_gateway/saveDefault",
                                "title" => "启用|弃用服务商",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "启用|弃用服务商",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-9",
                            ],
                            [
                                "href" => "/admin/sms_gateway/edit",
                                "title" => "编辑服务商",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "编辑服务商",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-10",
                            ],
                        ],
                    ],
                    [   //  短信模板
                        "href" => "/admin/sms_temp/list",
                        "title" => "模板",
                        "icon" => 'fa fa-file-text-o',
                        "type" => 2,
                        "status" => 1,
                        "remark" => "模板",
                        "target" => "_self",
                        "createtime" => $time,
                        'module' => $module,
                        "menus" => $module == "" ? $module : $module . "-11",
                        "contents" => [
                            [
                                "href" => "/admin/sms_temp/list",
                                "title" => "查看模板",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "查看模板",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-12",
                            ],
                            [
                                "href" => "/admin/sms_temp/ajaxList",
                                "title" => "异步获取模板信息",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "异步获取模板信息",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-13",
                            ],
                            [
                                "href" => "/admin/sms_temp/del",
                                "title" => "删除模板",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "删除模板",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-14",
                            ],
                            [
                                "href" => "/admin/sms_temp/edit",
                                "title" => "新增|编辑模板",
                                "icon" => 'fa fa-window-maximize',
                                "type" => 3,
                                "status" => 1,
                                "remark" => "新增|编辑模板",
                                "target" => "_self",
                                "createtime" => $time,
                                'module' => $module,
                                "menus" => $module == "" ? $module : $module . "-15",
                            ],
                        ],
                    ],
                ]
            ],
        ];
    }
}