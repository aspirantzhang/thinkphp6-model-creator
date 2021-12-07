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

        $configMock = m::mock('alias:think\facade\Config');
        $configMock->shouldReceive('get')->andReturnUsing(function (string $name, $default = null) {
            $reserved = [
                'reserved_field' => [
                    'id',
                    'title',
                    'pathname',
                    'list_order',
                    'create_time',
                    'update_time',
                    'delete_time',
                    'status',
                    '_id',
                    'original_id',
                    'lang_code',
                    'translate_time'
                ],
                'reserved_table' => [
                    'admin',
                    'admin_i18n',
                ],
            ];
            switch ($name) {
                case 'reserved':
                    return $reserved;
                case 'reserved.reserved_field':
                    return $reserved['reserved_field'];
                case 'reserved.reserved_table':
                    return $reserved['reserved_table'];
                default:
                    return null;
            }
        });
    }

    public function getDemo(string $name)
    {
        $jsonFile = createPath(__DIR__, 'json', $name) . '.json';
        if ($this->fileSystem->exists($jsonFile)) {
            $content = file_get_contents($jsonFile);
            return json_decode($content, true);
        }
        throw new \Exception('read demo file path failed: ' . $jsonFile);
    }

    protected function tearDown(): void
    {
    }
}
