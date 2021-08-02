<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

class GroupRuleTest extends BaseCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testAddRulesToGroup()
    {
        try {
            (new GroupRule())->addRulesToGroup([100,101], 1);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function testAddRulesToGroupFailed()
    {
        try {
            (new GroupRule())->addRulesToGroup([100,101], 1);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), 'failed to add rules to group');
            return;
        }
        $this->fail();
    }
}
