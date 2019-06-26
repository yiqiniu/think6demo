<?php

namespace yiqiniu\console\command;


use think\console\command\Make;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;

class SocketServer extends Make
{

    protected function configure()
    {
        parent::configure();
        $this->setName('socketserver')
            ->addArgument('commandName', Argument::OPTIONAL, "The name of the command")
            ->addArgument('port', Argument::OPTIONAL, "socket server bind Port ")
            ->setDescription('Create a new Socket Server command class');
    }

    protected function buildClass(string $name)
    {
        $stub = file_get_contents($this->getStub());

        $namespace = trim(implode('\\', array_slice(explode('\\', $name), 0, -1)), '\\');

        $class = str_replace($namespace . '\\', '', $name);

        return str_replace(['{%className%}', '{%actionSuffix%}', '{%namespace%}', '{%app_namespace%}'], [
            $class,
            $this->app->config->get('route.action_suffix'),
            $namespace,
            $this->app->getNamespace(),
        ], $stub);
    }



    protected function execute(Input $input, Output $output)
    {
        $name = trim($input->getArgument('name'));

        $classname = $this->getClassName($name);

        $pathname = $this->getPathName($classname);

        if (is_file($pathname)) {
            $output->writeln('<error>' . $this->type . ':' . $classname . ' already exists!</error>');
            return false;
        }

        if (!is_dir(dirname($pathname))) {
            mkdir(dirname($pathname), 0755, true);
        }

        file_put_contents($pathname, $this->buildClass($classname));

        $output->writeln('<info>' . $this->type . ':' . $classname . ' created successfully.</info>');
    }
    protected function getStub(): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . 'socket.stub';
    }

    protected function getNamespace(string $app): string
    {
        return parent::getNamespace($app) . '\\service';
    }
}
