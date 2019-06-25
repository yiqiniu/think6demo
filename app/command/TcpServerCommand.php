<?php

namespace app\command;


use app\admin\socket\TcpServer;
use think\console\Command;
use think\console\input\Argument;
use yiqiniu\traits\CommandService;


class TcpServerCommand extends Command
{

    use CommandService;

    // 命令参数
    protected $command = [
        'name' => 'yqn:tcpServer',
        'description' => 'Swoole HTTP Server for ThinkPHP',
    ];

    //处理类
    protected  $socket_class = '\app\admin\socket\TcpServer';






}
