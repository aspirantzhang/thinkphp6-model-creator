<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

use think\Exception;

class Helper
{
    public function existMysqlReservedKeywords(array $fieldNames)
    {
        $mysqlReservedKeywords = include createPath(__DIR__, 'helper', 'mysqlReservedKeywords') . '.php';
        $intersect = array_intersect($mysqlReservedKeywords, $fieldNames);
        return !empty($intersect);
    }
}
