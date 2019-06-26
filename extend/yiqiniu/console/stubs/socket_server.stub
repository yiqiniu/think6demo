<?php


namespace app\service;


use yiqiniu\facade\Logger;
use yiqiniu\swoole\BaseSocket;


class TcpServer extends BaseSocket
{



    /**
     * 处理Socket 收到的信息
     * @param $server
     * @param $fd
     * @param $from_id
     * @param $data
     */
    public function onReceive($server, $fd, $from_id, $recvdata)
    {
        try {
            dump($recvdata);

        } catch (\Exception $e) {
            Logger::exception($e);
            echo $e->getMessage();
        }

    }


}