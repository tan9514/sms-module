<?php
/**
 * Created By PhpStorm.
 * User: Li Ming
 * Date: 2021-06-25 15:44
 * Fun:
 * todo::设置表有问题  暂时不启用，后面修改
 */

namespace Modules\Sms\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Liming\Sms\Database\Factories\SmsLogFactory;

class Setting extends BaseModel
{
    protected $table = "setting";
}