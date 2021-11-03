<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

use think\Exception;

class TableTest extends BaseCase
{
    protected $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = [
            'name' => 'table-test',
            'title' => 'Table Test',
        ];
    }

    public function testCreateModelTable()
    {
        (new Table())->init($this->config)->createModelTable();
        $this->assertTrue(true);
    }

    /**
    * @depends testCreateModelTable
    */
    public function testCreateModelTableFailed()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('create model table failed: tableName=table-test');
        (new Table())->init($this->config)->createModelTable();
    }

    /**
    * @depends testCreateModelTableFailed
    */
    public function testRemoveModelTable()
    {
        (new Table())->init($this->config)->removeModelTable();
        $this->assertTrue(true);
    }
}
