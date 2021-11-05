<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

if (!function_exists('base_path')) {
    function base_path()
    {
        return createPath(dirname(__DIR__, 3), 'runtime');
    }
}
if (!function_exists('root_path')) {
    function root_path()
    {
        return createPath(dirname(__DIR__, 3), 'runtime');
    }
}
class BaseCase extends \aspirantzhang\octopusModelCreator\TestCase
{
    protected $singleMainTableConfig;
    protected $mainTableOfCategoryTypeConfig;

    protected function setUp(): void
    {
        $this->singleMainTableConfig = [
            'name' => 'unit-test',
            'title' => 'Unit Test',
        ];
        $this->mainTableOfCategoryTypeConfig = [
            'name' => 'main_table_of_category',
            'title' => 'Main Table of Category',
            'type' => 'mainTableOfCategory',
            'categoryTableName' => 'category_table_of_category',
        ];
        parent::setUp();
    }
}
