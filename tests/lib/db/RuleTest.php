<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

class RuleTest extends BaseCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateRule()
    {
        try {
            $id = (new Rule())->createRule('Rule Test');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return $id;
    }
}
