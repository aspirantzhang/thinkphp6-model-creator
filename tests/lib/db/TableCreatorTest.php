<?php

namespace aspirantzhang\octopusModelCreator\lib\db;

use think\Exception;
use ReflectionClass;

class TableCreatorTest extends BaseCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testDefaultSqlIsEmpty()
    {
        $tableCreator = new TableCreator('unit-test');
        $this->assertEquals('', $tableCreator->getSql());
    }

    public function testBuildSqlOfTypeMain()
    {
        $tableCreator = new TableCreator('unit-test');
        $tableCreator->buildSql();
        $this->assertStringStartsWith('CREATE TABLE `unit-test` (', $tableCreator->getSql());
    }
}
