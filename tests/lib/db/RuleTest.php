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
        try {
            $id = (new Rule())->init('rule-test', 'Rule Test')->createRule();
            $this->assertTrue(true);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $id;
    }

    /**
    * @depends testCreateRule
    */
    public function testCreateChildrenRules($id)
    {
        try {
            (new Rule())->init('rule-test', 'Rule Test')->createChildrenRules($id);
            $this->assertTrue(true);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $id;
    }
    /**
    * @depends testCreateChildrenRules
    */
    public function testRemoveRuleSuccessfully($id)
    {
        try {
            (new Rule())->removeRules($id);
            $this->assertTrue(true);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
