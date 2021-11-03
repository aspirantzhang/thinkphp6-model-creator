<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\command\file;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;
use aspirantzhang\octopusModelCreator\ModelCreator;
use think\Exception;

class Remove extends Command
{

    protected function configure()
    {
        $this->setName('file:remove')
            ->addArgument('tableName', Argument::REQUIRED, "Table name")
            ->setDescription('Remove files of a model');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('<info>Removing model files...</info>');

        $tableName = trim($input->getArgument('tableName'));

        try {
            ModelCreator::file()->config([
                'name' => $tableName,
                'title' => $tableName
            ])->remove();
            $output->writeln('<info>...Complete successfully.</info>');
        } catch (Exception $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
        }
    }
}
