<?php

use think\facade\Env;

return [
    'default' => Env::get('filesystem.driver', 'qiniu'),
    'disks' => [
        'local' => [
            'type' => 'local',
            'root' => app()->getRuntimePath() . 'storage',
        ],
        'public' => [
            'type' => 'local',
            'root' => app()->getRootPath() . 'public/storage',
            'url' => '/storage',
            'visibility' => 'public',
        ],
        // 更多的磁盘配置信息
        //七牛云
        'qiniu' => [
            'type' => '\yiqiniu\filesystem\qiniu',
            'cdnhost' => 'http://pthssnfmi.bkt.clouddn.com/',
            'bucket' => 'test',
            'accesskey' => 'VHoUJiJCQGFFPkC8OQhhUdDyErfB3gXLBhlFY25s',
            'secretKey' => 'oMBuWzub4JCa1d3lj28a2HauKvBHPKKCZm4gfqiW'
        ]
        // 更多的磁盘配置信息
    ],
];
