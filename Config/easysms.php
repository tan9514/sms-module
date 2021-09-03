<?php
/**
 * Created By PhpStorm.
 * User: Li Ming
 * Date: 2021-06-28 16:33
 * Fun: overtrue/easy-sms 包发送短信配置, 不要在里面新增参数
 */

return [
    // HTTP 请求的超时时间（秒）
    'timeout' => 5.0,

    // 默认发送配置
    'default' => [
        // 网关调用策略，默认：顺序调用
        'strategy' => \Overtrue\EasySms\Strategies\OrderStrategy::class,

        // 默认可用的发送网关
        'gateways' => [],
    ],

    // 可用的网关配置
    'gateways' => [
        'errorlog' => [
            'file' => '/tmp/easy-sms.log',
        ]
    ],
];