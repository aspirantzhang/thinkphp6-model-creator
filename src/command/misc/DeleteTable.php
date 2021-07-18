<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\command\misc;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\input\Option;
use think\console\Output;
use think\helper\Str;
use think\facade\Config;
use think\facade\Db;

class DeleteTable extends Command
{

    protected function configure()
    {
        $this->setName('misc:deleteTable')
            ->setDescription('Delete tables');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('<info>Deleting reserved table\'s data...</info>');

        Config::load('api/common/reserved', 'reserved');
        $tables = Config::get('reserved.reserved_table');
        if (!empty($tables)) {
            foreach ($tables as $table) {
                $output->writeln('-> ' . $table);
                Db::execute("DROP TABLE IF EXISTS " . $table);
            }
        }
        Db::execute("DROP TABLE IF EXISTS `unit_test`");

        $output->writeln('<info>...Done.</info>');
    }
}
