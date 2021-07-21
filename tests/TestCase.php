<?php

namespace aspirantzhang\octopusModelCreator;

use Mockery as m;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function setUp(): void
    {
        $langMock = m::mock('alias:think\facade\Lang');
        $langMock->shouldReceive('get')->andReturn('Valid translation');
        $langMock->shouldReceive('load')->andReturn();

        $configMock = m::mock('alias:think\facade\Config');
        $configMock->shouldReceive('get')->andReturn('Valid Config');
    }
    protected function tearDown(): void
    {
    }
}
