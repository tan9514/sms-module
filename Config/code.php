<?php
/**
 * Created By PhpStorm.
 * User: Li Ming
 * Date: 2021-06-28 10:30
 * Fun:
 */

return [
    "error" => 10000,

    "setting" => [
        "is_open" => 1, // 短信发送功能是否开启: 1=开启  2=关闭
        "interval" => 1, // 同一个手机号距离上次发送短信间隔时间（分钟）
        "minute_max" => 1, // 同一个手机号每分钟最多可以发送短信次数
        "hous_max" => 5, // 同一个手机号每小时最多可以发送短信次数
        "day_max" => 10, // 同一个手机号每天最多可以发送短信次数
    ],
];