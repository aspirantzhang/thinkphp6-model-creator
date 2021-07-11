<?php

declare(strict_types=1);

namespace aspirantzhang\thinkphp6ModelCreator\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\helper\Str;

class Model extends Command
{
    protected $appPath;

    protected function configure()
    {
        $this->setName('make:buildModel')
            ->addArgument('model', Argument::REQUIRED, "Model Name")
            ->addOption('route', null, Option::VALUE_OPTIONAL, 'Route name')
            ->setDescription('Fast generate model files.');
    }

    protected function execute(Input $input, Output $output)
    {
        
        $this->appPath = $this->app->getBasePath();

        $modelName = trim($input->getArgument('model')) ?: '';
        $modelName = parse_name($modelName, 1);
        
        $instanceName = parse_name($modelName, 1, false);

        $tableName = Str::snake($modelName);

        if ($input->hasOption('route')) {
            $routeName = trim($input->getOption('route'));
        } else {
            $routeName = $tableName . 's';
        }
        $output->writeln('<info>Writing [' . $modelName . ']...</info>');

        $this->buildModel($modelName, $instanceName, $tableName, $routeName);

        $output->writeln('<info>...Done.</info>');
    }

    protected function buildModel($modelName, $instanceName, $tableName, $routeName): void
    {

        $type = ['controller', 'model', 'view', 'logic', 'service', 'route', 'validate'];

        foreach ($type as $type) {
            $filename = $this->appPath . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . $modelName . '.php';

            if (!is_file($filename)) {
                $content = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . $type . '.stub');
                $content = str_replace(['{%modelName%}', '{%instanceName%}', '{%tableName%}', '{%routeName%}'], [$modelName, $instanceName, $tableName, $routeName], $content);
                $this->checkDirBuild(dirname($filename));
                file_put_contents($filename, $content);
            }
        }
    }

    protected function checkDirBuild(string $dirname): void
    {
        if (!is_dir($dirname)) {
            mkdir($dirname, 0755, true);
        }
    }
}
