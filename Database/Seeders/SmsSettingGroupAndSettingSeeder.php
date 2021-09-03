<?php
namespace Modules\Sms\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * @author liming
 * @date 2021-07-09 14:50
 * todo::短信发送配置有问题  没有接入
 */
class SmsSettingGroupAndSettingSeeder extends Seeder
{
    public function run()
    {
        if (Schema::hasTable('setting_group') && Schema::hasTable('setting')){
            // 开启事务
            DB::beginTransaction();
            try {
                $data = $this->defaultInfo();
                if(!empty($data)) {
                    // 新增配置组
                    $groupData = $data["setting_group"];
                    $groupInfo = DB::table('setting_group')->where([
                        ["plugin_type", "=", $groupData["plugin_type"]],
                    ])->first();
                    if(!$groupInfo){
                        $groupId = DB::table('setting_group')->insertGetId($groupData);
                    }else{
                        $groupId = $groupInfo->id;
                    }
                    if(!$groupId || $groupId <= 0) throw new \Exception("新增包配置组失败");

                    // 新增配置
                    $settingData = $data["setting"];
                    foreach ($settingData as $setting){
                        $setting["setting_group_id"] = $setting["setting_group_id"] > 0 ? $setting["setting_group_id"] : $groupId;
                        $settingId = DB::table('setting')->insertGetId($setting);
                        if(!$settingId || $settingId <= 0) throw new \Exception("新增包配置信息失败");
                    }
                }

                DB::commit();
            }catch (\Exception $e){
                DB::rollBack();
            }
        }
    }

    /**
     * 新增短信模板变量信息
     */
    private function defaultInfo()
    {
        $plugin_type = config('smsconfig.module') ?? "";
        if($plugin_type == "") return [];

        $time = date("Y-m-d H:i:s");
        return [
            "setting_group" => [
                "module" => $plugin_type,
                "name" => "短信插件",
                "description" => "composer插件包：集合后台管理，前端发送短信接口，验证短信验证码接口功能",
                "code" => $plugin_type,
                "plugin_type" => $plugin_type,
                "created_at" => $time,
                "updated_at" => $time,
            ],

            "setting" => [
                [
                    "setting_group_id" => 0,
                    "name" => "是否开启",
                    "description" => "短信发送功能是否开启: 1=开启  2=关闭",
                    "code" => "is_open",
                    "content" => json_encode([
                        "type" => "radio",
                        "list" => [
                            1 => "开启",
                            2 => "关闭",
                        ],
                        "value" => 1
                    ]),
                    "plugin_type" => $plugin_type,
                    "created_at" => $time,
                    "updated_at" => $time,
                ],
                [
                    "setting_group_id" => 0,
                    "name" => "发送间隔",
                    "description" => "同一个手机号距离上次发送短信间隔时间（分钟）",
                    "code" => "interval",
                    "content" => json_encode([
                        "type" => "number",
                        "min" => 1,
                        "max" => 5,
                        "value" => 1
                    ]),
                    "plugin_type" => $plugin_type,
                    "created_at" => $time,
                    "updated_at" => $time,
                ],
                [
                    "setting_group_id" => 0,
                    "name" => "分钟最多次数",
                    "description" => "同一个手机号每分钟最多可以发送短信次数",
                    "code" => "minute_max",
                    "content" => json_encode([
                        "type" => "number",
                        "min" => 1,
                        "max" => 1,
                        "value" => 1
                    ]),
                    "plugin_type" => $plugin_type,
                    "created_at" => $time,
                    "updated_at" => $time,
                ],
                [
                    "setting_group_id" => 0,
                    "name" => "小时最多次数",
                    "description" => "同一个手机号每小时最多可以发送短信次数",
                    "code" => "hous_max",
                    "content" => json_encode([
                        "type" => "number",
                        "min" => 1,
                        "max" => 5,
                        "value" => 5
                    ]),
                    "plugin_type" => $plugin_type,
                    "created_at" => $time,
                    "updated_at" => $time,
                ],
                [
                    "setting_group_id" => 0,
                    "name" => "自然日最多次数",
                    "description" => "同一个手机号每天最多可以发送短信次数",
                    "code" => "day_max",
                    "content" => json_encode([
                        "type" => "number",
                        "min" => 1,
                        "max" => 40,
                        "value" => 10
                    ]),
                    "plugin_type" => $plugin_type,
                    "created_at" => $time,
                    "updated_at" => $time,
                ],
            ]
        ];
    }
}