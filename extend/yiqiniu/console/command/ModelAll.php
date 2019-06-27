<?php


namespace yiqiniu\console\command;


use think\console\command\Make;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;
use think\facade\Db;

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

    protected function configure()
    {
        $this->setName('make:modelall')
            ->addArgument('m', Argument::OPTIONAL, "specified Module name")
            ->setDescription('make database  all table  ');
    }


    protected function execute(Input $input, Output $output)
    {

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
        //select table_name,table_comment from information_schema.tables where table_schema='yiqiniu_new';
        $tablelist = Db::connect($default ?: $connect)->table('information_schema.tables')
            ->where('table_schema', $connect['database'])
            ->field('table_name as name,table_comment as comment')
            ->select();

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
        $namespace = $this->getNamespace($name);

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
            $class_name = $this->app->parseName(substr($table['name'], $prefix_len), 1, true);
            // 如果是表名是class的改为ClassModel

            $tablename = '';
            if ($class_name == 'Class') {
                $class_name = 'ClassModel';
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

    protected function getNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\model';
    }

    protected function getStub()
    {

        foreach ($this->stubs as $key => $filename) {

            $this->stubs[$key] = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . $filename . '.stub';
        }
        return $this->stubs;
    }
}