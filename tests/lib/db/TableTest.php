<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

use think\Exception;

class TableTest extends BaseCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateModelTable()
    {
        (new Table())->init('unit-test-1', 'Unit Test 1')->createModelTable();
        $this->assertTrue(true);
    }

    /**
    * @depends testCreateModelTable
    */
    public function testCreateModelTableFailed()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('create model table failed: tableName=unit-test-1');
        (new Table())->init('unit-test-1', 'Unit Test 1')->createModelTable();
    }

    /**
    * @depends testCreateModelTableFailed
    */
    public function testRemoveModelTable()
    {
        (new Table())->init('unit-test-1', 'Unit Test 1')->removeModelTable();
        $this->assertTrue(true);
    }
}
