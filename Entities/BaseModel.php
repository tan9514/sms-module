<?php
/**
 * Created By PhpStorm.
 * User: Li Ming
 * Date: 2021-06-25 15:44
 * Fun:
 */

namespace Modules\Sms\Entities;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Liming\Sms\Database\Factories\SmsLogFactory;

class BaseModel extends Model
{
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format($this->dateFormat ?: 'Y-m-d H:i:s');
    }
}