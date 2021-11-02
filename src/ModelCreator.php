<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

class ModelCreator
{
    public static function file()
    {
        return (new File());
    }

    public static function db(string $tableName, string $modelTitle = '')
    {
        return (new Db())->init($tableName, $modelTitle);
    }
}
