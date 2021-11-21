<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

use think\Exception;

class Helper
{
    public function checkContainsMysqlReservedKeywords(array $fieldNames)
    {
        $mysqlReservedKeywords = include createPath(__DIR__, 'helper', 'mysqlReservedKeywords') . '.php';
        $intersect = array_intersect($mysqlReservedKeywords, $fieldNames);
        if (!empty($intersect)) {
            throw new Exception(__('mysql reserved keyword', ['keyword' => implode(',', $intersect)]));
        };
    }
}
