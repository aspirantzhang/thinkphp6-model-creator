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
    protected $defaultConfig;

    protected function setUp(): void
    {
        $this->defaultConfig = [
            'name' => 'unit-test',
            'title' => 'Unit Test',
        ];
        parent::setUp();
    }
}
