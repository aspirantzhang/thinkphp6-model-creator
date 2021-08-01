<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

class ModelTableTest extends BaseCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateModelTable()
    {
        try {
            (new ModelTable())->init('unit-test-1', 'Unit Test 1')->createModelTable();
            $this->assertTrue(true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
    * @depends testCreateModelTable
    */
    public function testCreateModelTableFailed()
    {
        try {
            (new ModelTable())->init('unit-test-1', 'Unit Test 1')->createModelTable();
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), 'create model table failed: tableName=unit-test-1');
            return;
        }
        $this->fail();
    }

    /**
    * @depends testCreateModelTableFailed
    */
    public function testRemoveModelTable()
    {
        try {
            (new ModelTable())->init('unit-test-1', 'Unit Test 1')->removeModelTable();
            $this->assertTrue(true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
