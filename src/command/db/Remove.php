<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\command\db;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use aspirantzhang\octopusModelCreator\ModelCreator;
use think\Exception;

class Remove extends Command
{
    protected function configure()
    {
        $this->setName('db:remove')
            ->addArgument('tableName', Argument::REQUIRED, "Table name")
            ->addOption('topRuleId', null, Option::VALUE_REQUIRED, 'Top rule id')
            ->addOption('topMenuId', null, Option::VALUE_REQUIRED, 'Top Menu id')
            ->setDescription('Remove table and related records of the model');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('<info>Processing...</info>');

        $tableName = trim($input->getArgument('tableName'));
        $topRuleId = $input->getOption('topRuleId');
        $topMenuId = $input->getOption('topMenuId');

        try {
            ModelCreator::db($tableName)->remove((int)$topRuleId, (int)$topMenuId);
            $output->writeln('<info>...Complete successfully.</info>');
        } catch (Exception $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
        }
    }
}
