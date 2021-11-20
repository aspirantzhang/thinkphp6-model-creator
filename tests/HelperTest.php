<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

use think\Exception;
use aspirantzhang\octopusModelCreator\TestCase;

class HelperTest extends TestCase
{
    public function testExistMysqlReservedKeywords()
    {
        $this->assertTrue(ModelCreator::helper()->existMysqlReservedKeywords(['all']));
        $this->assertTrue(ModelCreator::helper()->existMysqlReservedKeywords(['like', 'left', 'ok']));
        $this->assertFalse(ModelCreator::helper()->existMysqlReservedKeywords(['ok']));
    }
}
