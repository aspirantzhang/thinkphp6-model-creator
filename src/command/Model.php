<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\command;

use think\console\Command;
use think\console\Input;
use think\console\input\Argument;
use think\console\Output;
use aspirantzhang\octopusModelCreator\File;

class Model extends Command
{

    protected function configure()
    {
        $this->setName('make:modelFile')
            ->addArgument('tableName', Argument::REQUIRED, "Table Name")
            ->setDescription('Generate model files');
    }

    protected function execute(Input $input, Output $output)
    {
        $tableName = trim($input->getArgument('tableName')) ?: '';

        $output->writeln('<info>Writing [' . $tableName . ']...</info>');

        $file = new File();
        try {
            $file->make($tableName);
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }

        $output->writeln('<info>...Done.</info>');
    }
}
