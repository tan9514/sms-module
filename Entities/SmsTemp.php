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

class SmsTemp extends BaseModel
{
    use HasFactory;
    protected $table = "sms_temp";
    protected $guarded = [];

    /**
     * 反向关联 短信服务商 Model
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function gateway()
    {
        return $this->belongsTo('Modules\Sms\Entities\SmsGateway');
    }

    public static function getStatusArr()
    {
        return [
            "1" => "等待审核",
            "2" => "审核成功",
            "3" => "审核失败",
        ];
    }

    /**
     * 设置模板状态
     * @return string
     */
    public function getStatusNameAttribute()
    {
        return self::getStatusArr()[$this->status] ?? "";
    }
}