<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

use think\Exception;

class RuleTest extends BaseCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateRule()
    {
        $id = (new Rule())->init('rule-test', 'Rule Test')->createRule();
        $this->assertTrue(true);
        return $id;
    }

    /**
    * @depends testCreateRule
    */
    public function testCreateChildrenRules($id)
    {
        (new Rule())->init('rule-test', 'Rule Test')->createChildrenRules($id);
        $this->assertTrue(true);
        return $id;
    }
    /**
    * @depends testCreateChildrenRules
    */
    public function testRemoveRuleSuccessfully($id)
    {
        (new Rule())->removeRules($id);
        $this->assertTrue(true);
    }
}
