<?php
/**
 * Created By PhpStorm.
 * User: Li Ming
 * Date: 2021-06-25 15:44
 * Fun:
 */

namespace Modules\Sms\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class SmsGateway extends BaseModel
{
    use HasFactory;

    protected $guarded = [];

    /**
     * 验证是否可以启用
     * @param $id
     */
    public static function checkStatus($id)
    {
        $info = self::find($id);
        if(!$info) return ['msg' => '数据不存在'];

        $params = unserialize($info->params);
        $status = true;
        foreach($params as $k => $item){
            if(!is_array($item) && $item == ''){
                $status = false;
                break;
            }
        }

        if(!$status) return ['msg' => '请先设置配置参数'];
        return $status;
    }
}