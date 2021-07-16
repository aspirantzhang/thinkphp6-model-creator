<?php

declare(strict_types=1);

namespace aspirantzhang\thinkphp6ModelCreator;

class ModelCreator
{
    public static function file(string $tableName, string $modelTitle, string $currentLang)
    {
        return (new File())->init($tableName, $modelTitle, $currentLang);
    }

    public static function db(string $tableName, string $modelTitle, string $currentLang)
    {
        return (new Db())->init($tableName, $modelTitle, $currentLang);
    }
}
