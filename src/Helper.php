<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

use think\Exception;
use think\facade\Config;

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

    public function checkContainsReservedFieldNames(array $fieldNames)
    {
        $reservedFieldNames = Config::get('reserved.reserved_field') ?? [];
        $intersect = array_intersect($reservedFieldNames, $fieldNames);
        if (!empty($intersect)) {
            throw new Exception(__('reserved field name', ['fieldName' => implode(',', $intersect)]));
        }
    }
}
