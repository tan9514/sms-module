<?php
namespace Modules\Sms\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * @author liming
 * @date 2021-07-02 10:50
 */
class SmsVariableSeeder extends Seeder
{
    public function run()
    {
        if (Schema::hasTable('sms_variable')){
            $info = DB::table('sms_variable')->where('id', '>', 0)->first();
            if(!$info){
                $arr = $this->defaultInfo();
                if(!empty($arr) && is_array($arr)) {
                    $created_at = $updated_at = date("Y-m-d H:i:s");
                    foreach ($arr as $item) {
                        DB::table('sms_variable')->insert([
                            'key' => $item['key'] ?? "",
                            'remark' => $item["remark"] ?? "",
                            'created_at' => $created_at,
                            'updated_at' => $updated_at,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * 新增短信模板变量信息
     */
    private function defaultInfo()
    {
        return [
            ["key" => "code", "remark" => "短信验证码"],
            ["key" => "orderno", "remark" => "订单编号"],
            ["key" => "nickname", "remark" => "会员昵称"]
        ];
    }
}