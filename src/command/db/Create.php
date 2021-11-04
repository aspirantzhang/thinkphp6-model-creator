<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\command\db;

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
        $this->setName('db:create')
            ->addArgument('tableName', Argument::REQUIRED, "Table name")
            ->addArgument('modelTitle', Argument::OPTIONAL, "Model title")
            ->setDescription('Create table for new model');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('<info>Creating model database...</info>');

        $tableName = trim($input->getArgument('tableName'));
        $modelTitle = $input->getArgument('modelTitle') ? trim($input->getArgument('modelTitle')) : $tableName;

        try {
            $result = ModelCreator::db()->config([
                'name' => $tableName,
                'title' => $modelTitle
            ])->create();
            $output->writeln('<info>' . print_r($result) . '</info>');
            $output->writeln('<info>...Complete successfully.</info>');
        } catch (Exception $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
        }
    }
}
