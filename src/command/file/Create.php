<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\command\file;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;
use aspirantzhang\octopusModelCreator\ModelCreator;
use think\Exception;

class Create extends Command
{

    protected function configure()
    {
        $this->setName('file:create')
            ->addArgument('tableName', Argument::REQUIRED, "Table name")
            ->addArgument('modelTitle', Argument::OPTIONAL, "Model title")
            ->setDescription('Create basic files of a model');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('<info>Creating model files...</info>');

        $tableName = trim($input->getArgument('tableName'));
        $modelTitle = $input->getArgument('modelTitle') ? trim($input->getArgument('modelTitle')) : $tableName;

        try {
            ModelCreator::file()->config([
                'name' => $tableName,
                'title' => $modelTitle
            ])->create();
            $output->writeln('<info>...Complete successfully.</info>');
        } catch (Exception $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
        }
    }
}
