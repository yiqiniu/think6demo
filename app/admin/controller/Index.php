<?php

namespace app\admin\controller;

use app\BaseController;
use ReflectionClass;
use think\App;
use think\facade\Filesystem;


class Index extends BaseController
{
    /**
     *
     * @return string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index()
    {
        /* $list = Db::name('weixin_menu')->selectArray();


         $list2 = WeixinMenu::where("status", 1)->selectArray();
         $list3 = WeixinMenu::test();

         Logger::log($list);
         dump($list2);
         exit;*/

        //return view();
        $data = ['username' => '111', 'password' => '22222'];
        queue('app\\admin\\queue\\Job1@task1', $data);

        echo time();
        //queue('app\\admin\\queue\\Job1@task2',$data);
        //queue('app\\admin\\queue\\Job1@task3',$data);


        // return '<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} a{color:#2E5CD5;cursor: pointer;text-decoration: none} a:hover{text-decoration:underline; } body{ background: #fff; font-family: "Century Gothic","Microsoft yahei"; color: #333;font-size:18px;} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.6em; font-size: 42px }</style><div style="padding: 24px 48px;"> <h1>:) </h1><p> ThinkPHP V6<br/><span style="font-size:30px">13载初心不改 - 你值得信赖的PHP框架</span></p></div><script type="text/javascript" src="https://tajs.qq.com/stats?sId=64890268" charset="UTF-8"></script><script type="text/javascript" src="https://e.topthink.com/Public/static/client.js"></script><think id="eab4b9f840753f8e7"></think>';
    }

    public function hello($name = 'ThinkPHP6')
    {
        //return 'hello,' . $name;

        /*   $data=['username'=>'111','password'=>'22222'];
           queue('app\\admin\\queue\\Job1@task1',$data);
           queue('app\\admin\\queue\\Job1@task2',$data);
           queue('app\\admin\\queue\\Job1@task3',$data);*/
    }


    public function upload()
    {

        if ($this->request->isPost()) {
            // 获取表单上传文件 例如上传了001.jpg
            $file = request()->file('image');
            // 上传到本地服务器
            $savename = Filesystem::putFile('topic', $file);
            dump($savename);
            return;
        }

    }

    public function facade()
    {
        try {

            $class_name = 'yiqiniu\library\Redis';

            $baseClass = App::classBaseName($class_name);

            // 解析当前类
            $ref = new ReflectionClass($class_name);
            $methods = $ref->getMethods();
            $funs = [];
            //解决类的所有public方法
            foreach ($methods as $method) {
                // 排除特殊的方法
                if (substr($method->name, 0, 2) == '__')
                    continue;
                if ($method->isPublic()) {
                    // 获取注释内容
                    $doccomment = $method->getDocComment();
                    $doccomment = str_replace("\r\n","\n",$doccomment);
                    if (strpos($doccomment, "\n") !== false) {
                        $doc = explode("\n", $method->getDocComment())[1];
                    } else {
                        $doc = $method->getDocComment();
                    }
                    $funs[$method->name]['comment'] = str_replace(' * ', '', $doc);
                    //函数名称
                    $funs[$method->name]['name'] = $method->getName();
                    // 返回值
                    $returnType = $method->getReturnType();
                    $funs[$method->name]['return'] = empty($returnType) ? 'mixed' : $returnType->getName();
                    // 参数
                    $parameters = $method->getParameters();
                    $parameter_str = '';
                    $usedefault = false;
                    foreach ($parameters as $k => $param) {
                        $param_name = $param->name;
                        $type = $param->getType();
                        $param_type = empty($type) ? '' : $type->getName();
                        $param_default = '';
                        // 参数模板值
                        if ($param->isOptional()) {
                            $param_default = $param->getDefaultValue();
                            if ($param_type == 'bool' && !empty($param_default)) {
                                $param_default = $param_default ? 'true' : 'false';
                            }
                        }

                        if (empty($param_default) && $usedefault == false) {
                            $parameter_str .= $param_type . ' $' . $param_name . ',';
                        } else {
                            $param_default = empty($param_default) ? " ''" : $param_default;
                            $parameter_str .= $param_type . ' $' . $param_name . ' = ' . $param_default . ',';
                            $usedefault = true;
                        }

                    }
                    $funs[$method->name]['args'] = substr($parameter_str, 0, -1);
                }
            }


            $method_format = "\t* @method %s %s(%s) static %s \r\n";
            $method_str = '';
            foreach ($funs as $fun){
                $method_str .= sprintf($method_format,$fun['return'],$fun['name'],$fun['args'],$fun['comment']);
            }
            dump($method_str);

        } catch (\ReflectionException $e) {

            dump($e);
        }


    }
}
