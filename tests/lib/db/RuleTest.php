<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

class RuleTest extends BaseCase
{
    protected $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = [
            'name' => 'menu-test',
            'title' => 'Menu Test',
        ];
    }

    public function testCreateRule()
    {
        $id = (new Rule())->init($this->config)->createRule();
        $this->assertTrue(true);
        return $id;
    }

    /**
    * @depends testCreateRule
    */
    public function testCreateChildrenRules($id)
    {
        (new Rule())->init($this->config)->createChildrenRules($id);
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
