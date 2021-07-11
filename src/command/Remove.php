<?php

declare(strict_types=1);

namespace aspirantzhang\thinkphp6ModelCreator\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;

class Remove extends Command
{
    protected $appPath;

    protected function configure()
    {
        $this->setName('make:removeModel')
            ->addArgument('model', Argument::OPTIONAL, "Model Name")
            ->setDescription('Remove Model');
    }

    protected function execute(Input $input, Output $output)
    {
        $modelName = $input->getArgument('model') ?: '';
        $this->appPath = $this->app->getBasePath();

        $output->writeln('<info>Removing [' . $modelName . ']....</info>');
        $this->removeModel($modelName);
        $output->writeln("<info>...Done.</info>");
    }

    protected function removeModel(string $modelName): void
    {
        $modelName = parse_name($modelName, 1);

        $type = ['controller', 'model', 'view', 'logic', 'service', 'route', 'validate'];

        foreach ($type as $type) {
            $filename = $this->appPath . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $modelName . '.php';

            if (is_file($filename)) {
                unlink($filename);
            }
        }
    }
}
