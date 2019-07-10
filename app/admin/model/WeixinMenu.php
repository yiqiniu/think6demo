<?php


namespace app\admin\model;


class WeixinMenu extends BaseModel
{


    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function test(){

        return self::selectArray();
    }

}