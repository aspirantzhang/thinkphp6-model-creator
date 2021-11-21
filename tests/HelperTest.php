<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

use think\Exception;
use aspirantzhang\octopusModelCreator\TestCase;

class HelperTest extends TestCase
{
    public function testCheckContainsMysqlReservedKeywordsFailed()
    {
        $this->expectException(Exception::class);
        ModelCreator::helper()->checkContainsMysqlReservedKeywords(['all']);
        ModelCreator::helper()->checkContainsMysqlReservedKeywords(['like', 'left', 'notExist']);
    }

    public function testCheckContainsMysqlReservedKeywordsPassed()
    {
        $this->assertNull(ModelCreator::helper()->checkContainsMysqlReservedKeywords(['notExist']));
    }

    public function testCheckContainsReservedFieldNamesFailed()
    {
        $this->expectException(Exception::class);
        ModelCreator::helper()->checkContainsReservedFieldNames(['id']);
        ModelCreator::helper()->checkContainsReservedFieldNames(['id', 'create_time', 'notExist']);
    }

    public function testCheckContainsReservedFieldNamesPassed()
    {
        $this->assertNull(ModelCreator::helper()->checkContainsReservedFieldNames(['notExist']));
    }
}
