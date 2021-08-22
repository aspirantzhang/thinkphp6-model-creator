<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

use think\Exception;

class GroupRuleTest extends BaseCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testAddRulesToGroup()
    {
        (new GroupRule())->addRulesToGroup([100,101], 1);
        $this->assertTrue(true);
    }

    public function testAddRulesToGroupFailed()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('failed to add rules to group');
        (new GroupRule())->addRulesToGroup([100,101], 1);
    }
}
