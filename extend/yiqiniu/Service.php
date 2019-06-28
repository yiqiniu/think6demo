<?php


namespace yiqiniu;


use think\Route;
use yiqiniu\console\command\ModelAll;
use yiqiniu\console\command\Socket;
use yiqiniu\filesystem\Oss;
use yiqiniu\filesystem\Qiniu;

class Service extends \think\Service
{
    public function register()
    {
        $this->app->bind('qiniu', Qiniu::class);
        $this->app->bind('oss', Oss::class);
    }

    public function boot(Route $route)
    {
        $this->commands(Socket::class);
        $this->commands(ModelAll::class);
    }
}