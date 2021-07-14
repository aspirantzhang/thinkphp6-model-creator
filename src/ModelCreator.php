<?php

declare(strict_types=1);

namespace aspirantzhang\thinkphp6ModelCreator;

class ModelCreator
{
    public static function file(string $tableName, array $fileTypes = null)
    {
        return (new File())->init($tableName, $fileTypes);
    }
}
