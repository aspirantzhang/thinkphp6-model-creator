<?php

namespace aspirantzhang\octopusModelCreator;

use Symfony\Component\Filesystem\Filesystem;
use Mockery as m;

if (!function_exists('base_path')) {
    function base_path()
    {
        return createPath(dirname(__DIR__), 'runtime');
    }
}
if (!function_exists('root_path')) {
    function root_path()
    {
        return createPath(dirname(__DIR__), 'runtime');
    }
}
abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected $fileSystem;

    protected function setUp(): void
    {
        $this->fileSystem = new Filesystem();
        $langMock = m::mock('alias:think\facade\Lang');
        $langMock->shouldReceive('get')->andReturnUsing(function (string $name, array $vars = [], string $lang = '') {
            if (!empty($vars)) {
                return $name . ': ' . implode(';', array_map(function ($key, $value) {
                    return $key . '=' . $value;
                }, array_keys($vars), $vars));
            }
            return $name;
        });
        $langMock->shouldReceive('getLangSet')->andReturn('en-us');
        $langMock->shouldReceive('load')->andReturn();

        // $configMock = m::mock('alias:think\facade\Config');
        // $configMock->shouldReceive('get')->andReturn('Valid Config');
    }
    
    protected function tearDown(): void
    {
    }
}
