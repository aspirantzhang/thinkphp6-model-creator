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
    protected $mainConfig;
    protected $categoryConfig;

    protected function setUp(): void
    {
        $this->mainConfig = [
            'name' => 'unit-test',
            'title' => 'Unit Test',
        ];
        $this->categoryConfig = [
            'name' => 'category-table',
            'title' => 'Category Unit Test',
            'type' => 'category',
            'withRelation' => [
                'main-table'
            ],
        ];
        parent::setUp();
    }
}
