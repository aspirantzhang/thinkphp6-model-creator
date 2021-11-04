<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\command\db;

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
        $this->setName('db:remove')
            ->addArgument('tableName', Argument::REQUIRED, "Table name")
            ->addArgument('topRuleId', Argument::OPTIONAL, 'Top rule id')
            ->addArgument('topMenuId', Argument::OPTIONAL, 'Top Menu id')
            ->setDescription('Remove table and related records of the model');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('<info>Processing...</info>');

        $tableName = trim($input->getArgument('tableName'));
        $topRuleId = $input->getArgument('topRuleId') ? trim($input->getArgument('topRuleId')) : 0;
        $topMenuId = $input->getArgument('topMenuId') ? trim($input->getArgument('topMenuId')) : 0;

        try {
            ModelCreator::db()->config([
                'name' => $tableName,
                'title' => $tableName
            ])->remove((int)$topRuleId, (int)$topMenuId);
            $output->writeln('<info>...Complete successfully.</info>');
        } catch (Exception $e) {
            $output->writeln('<error>Error: ' . $e->getMessage() . '</error>');
        }
    }
}
