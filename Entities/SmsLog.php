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

class SmsLog extends BaseModel
{
    use HasFactory;
    protected $table = "sms_logs";
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
            "1" => "等待发送",
            "2" => "发送成功",
            "3" => "发送失败",
        ];
    }

    /**
     * 获取短信状态
     * @return string
     */
    public function getStatusNameAttribute()
    {
        return self::getStatusArr()[$this->status] ?? "";
    }
}