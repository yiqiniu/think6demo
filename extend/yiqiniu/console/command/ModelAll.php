<?php


namespace yiqiniu\console\command;


use think\App;
use think\console\command\Make;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;
use yiqiniu\facade\Db;

/**
 * Class ModelAll
 * @package yiqiniu\console\command
 */
class ModelAll extends Make
{

    protected $type = 'Command';

    // 基础Model的名称
    protected $baseModel = 'BaseModel';

    //配置模板
    protected $stubs = [
        'basemodel' => 'basemodel',
        'model' => 'model',
    ];

    protected $app=null;
    // 不能当做类名的表名

    protected $keywords = ['Abstract','Class','Traits'];

    protected function configure()
    {
        $this->setName('make:modelall')
            ->addArgument('m', Argument::OPTIONAL, "specified Module name")
            ->setDescription('make database  all table  ');
    }


    protected function execute(Input $input, Output $output)
    {

        $this->app= App::getInstance();
        $default = $this->app->config->get('database.default', '');
        if (!empty($default)) {
            $connect = $this->app->config->get('database.connections.' . $default);
        } else {
            $connect = $this->app->config->get('database.');
        }

        if (empty($connect['database'])) {
            $this->output->error('database not  setting.');
            return;
        }
        $tablelist = Db::connect($default?:$connect)->table('information_schema.tables')
            ->where('table_schema', $connect['database'])
            ->field('table_name as name,table_comment as comment')
            ->select();
        //select table_name,table_comment from information_schema.tables where table_schema='yiqiniu_new';

        // 获取数据库配置
        $name = trim($input->getArgument('m'));
        $apppath = $this->app->getAppPath();
        if (!empty($name)) {
            $dirname = $apppath . $name . '\\model\\';
        } else {
            $dirname = $apppath . 'model\\';
        }
        if (!file_exists($dirname)) {
            mkdir($dirname, 0644, true);
        }
        // 获取生成空间的名称
        $namespace = $this->getNamespace2($name);

        // 判断 是否有基本BaseModel

        $stubs = $this->getStub();
        // 写入基本的Model类
        $basemodel_file = $dirname . $this->baseModel . '.php';

        if (!file_exists($basemodel_file)) {
            $basemodel = file_get_contents($stubs['basemodel']);
            file_put_contents($basemodel_file, str_replace(['{%namespace%}', '{%className%}',], [
                $namespace,
                $this->baseModel,
            ], $basemodel));
        }


        // 生成所有的类
        $prefix_len = strlen($connect['prefix']);

        $model_stub = file_get_contents($stubs['model']);


        foreach ($tablelist as $k => $table) {
            $class_name = $this->parseName(substr($table['name'], $prefix_len), 1, true);
            // 如果是表名是class的改为ClassModel

            $tablename = '';
            if (in_array($class_name, $this->keywords)) {
                $class_name = $class_name.'Model';
                $tablename = "protected \$name='" . substr($table['name'], $prefix_len) . "';";
            }
            $model_file = $dirname . $class_name . '.php';
            if (!file_exists($model_file)) {
                file_put_contents($model_file, str_replace(['{%namespace%}', '{%className%}', '{%comment%}', '{%tablename%}'], [
                    $namespace,
                    $class_name,
                    $table['comment'],
                    $tablename
                ], $model_stub));
            }

        }


        $output->writeln('<info>' . $this->type . ':' . 'All Table Model created successfully.</info>');


    }

    protected function getNamespace2($app)
    {


        return  empty($app)?'app\\model':'app\\'.$app.'\\model';
    }


    protected function getStub()
    {

        foreach ($this->stubs as $key => $filename) {

            $this->stubs[$key] = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . $filename . '.stub';
        }
        return $this->stubs;
    }


    /**
     * 字符串命名风格转换
     * type 0 将Java风格转换为C的风格 1 将C风格转换为Java的风格
     * @access public
     * @param string  $name    字符串
     * @param integer $type    转换类型
     * @param bool    $ucfirst 首字母是否大写（驼峰规则）
     * @return string
     */
    public static function parseName($name = null, $type = 0, $ucfirst = true)
    {
        if ($type) {
            $name = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
                return strtoupper($match[1]);
            }, $name);
            return $ucfirst ? ucfirst($name) : lcfirst($name);
        }

        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
}