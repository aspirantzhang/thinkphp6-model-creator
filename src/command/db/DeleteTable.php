<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\command\db;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Config;
use think\facade\Db;

class DeleteTable extends Command
{

    protected function configure()
    {
        $this->setName('db:deleteReservedTable')
            ->setDescription('Delete reserved tables');
    }

    protected function execute(Input $input, Output $output)
    {
        $output->writeln('<info>Deleting reserved table\'s data...</info>');

        Config::load(createPath('api', 'common', 'reserved'), 'reserved');
        $tables = Config::get('reserved.reserved_table');
        if (!empty($tables)) {
            foreach ($tables as $table) {
                $output->writeln('-> ' . $table);
                Db::execute("DROP TABLE IF EXISTS " . $table);
            }
        }
        Db::execute("DROP TABLE IF EXISTS `unit_test`");

        $output->writeln('<info>...Done.</info>');

        return null;
    }
}
