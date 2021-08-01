<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

class ModelTableTest extends BaseCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateModelTableSuccessfully()
    {
        try {
            (new ModelTable())->init('unit-test', 'Unit Test')->createModelTable();
            $this->assertTrue(true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
    * @depends testCreateModelTableSuccessfully
    */
    public function testCreateModelTableFailed()
    {
        try {
            (new ModelTable())->init('unit-test', 'Unit Test')->createModelTable();
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), 'create model table failed: tableName=unit-test');
            return;
        }
        $this->fail();
    }
}
