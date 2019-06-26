<?php

namespace yiqiniu\console\command;


use think\console\command\Make;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

class SocketServer extends Make
{

    protected $type = 'Command';

    //配置模板
    protected $stubs = [
        'config'=>'socket_config',
        'command'=>'socket_command',
        'service'=>'socket_server'
    ];

    protected function configure()
    {
        parent::configure();
        $this->setName('socketserver')
            ->addArgument('commandName', Argument::REQUIRED, "The name of the command")
            ->addArgument('port', Argument::REQUIRED, "socket Port Number ")
            ->setDescription('Create a new Socket Server command class');
    }

    protected function buildClass(string $name,$stubpath='',$prot='')
    {
        $stub = file_get_contents(realpath($stubpath));

        return str_replace(['{%className%}','{%commandName%}', '{%prot%}'], [
            $name,
            strtolower($name),
            $prot
        ], $stub);
    }


    protected function execute(Input $input, Output $output)
    {
        $name = trim($input->getArgument('name'));
        $port = trim($input->getArgument('port'));

        $classname = $this->getClassName($name);

        $pathname = $this->getPathName($classname);

        if (is_file($pathname)) {
            $output->writeln('<error>' . $this->type . ':' . $classname . ' already exists!</error>');
            return false;
        }

        $apppath = $this->app->getAppPath();
        $command_file = $apppath.'command\\'.ucfirst($name).'Command.php';
        $service_file= $apppath.'service\\'.ucfirst($name).'Service.php';
        $config_file = $this->app->getConfigPath().strtolower($name).'.php';

        if(!file_exists($apppath.'command')){
            mkdir($apppath.'command');
        }
        if(!file_exists($apppath.'service')){
            mkdir($apppath.'service');
        }

        $this->getStub();

        foreach ($this->stubs as $k=>$v){

            if($k=='config' && !file_exists($config_file)){
                file_put_contents($config_file, $this->buildClass($name,$v,$port));
            }
            if($k=='command' && !file_exists($command_file)){
                file_put_contents($command_file, $this->buildClass($name,$v,$port));
            }
            if($k=='service' && !file_exists($service_file)){
                file_put_contents($service_file, $this->buildClass($name,$v,$port));
            }

        }


        $output->writeln('<info>' . $this->type . ':' . $classname . ' created successfully.</info>');
    }

    protected function getPathName(string $name): string
    {
        $name = str_replace('app\\', '', $name);

        return $this->app->getBasePath() . ltrim(str_replace('\\', '/', $name), '/') . '.php';
    }


    protected function getNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\service';
    }

    protected function getStub()
    {
        foreach ($this->stubs as $key=>$filename){
            $this->stubs[$key] = __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . $filename.'.stub';
        }
    }
}
