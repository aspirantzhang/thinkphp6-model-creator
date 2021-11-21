<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

use think\Exception;
use aspirantzhang\octopusModelCreator\TestCase;

class HelperTest extends TestCase
{
    public function testCheckContainsMysqlReservedKeywords()
    {
        $this->expectException(Exception::class);
        ModelCreator::helper()->checkContainsMysqlReservedKeywords(['all']);
        ModelCreator::helper()->checkContainsMysqlReservedKeywords(['like', 'left', 'ok']);
    }

    public function testCheckContainsMysqlReservedKeywordsSuccessfully()
    {
        $this->assertNull(ModelCreator::helper()->checkContainsMysqlReservedKeywords(['ok']));
    }
}
