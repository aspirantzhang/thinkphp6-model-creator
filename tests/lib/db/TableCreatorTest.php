<?php

namespace aspirantzhang\octopusModelCreator\lib\db;

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
        $tableCreator = new TableCreator('table-creator-main');
        $tableCreator->buildSql();
        $this->assertStringStartsWith('CREATE TABLE `table-creator-main` (', $tableCreator->getSql());
    }

    public function testCreateTableOfTypeMainWithExtra()
    {
        $tableCreator = new TableCreator('table-creator-main-extra');
        $tableCreator->setExtraFields([
            "extra-field-1",
            "extra-field-2",
        ]);
        $tableCreator->setExtraIndexes([
            "extra-index-1",
            "extra-index-2",
        ]);
        $tableCreator->buildSql();
        $this->assertStringContainsString('CREATE TABLE `table-creator-main-extra` (', $tableCreator->getSql());
        $this->assertStringContainsString('extra-field-1', $tableCreator->getSql());
        $this->assertStringContainsString('extra-field-2', $tableCreator->getSql());
        $this->assertStringContainsString('extra-index-1', $tableCreator->getSql());
        $this->assertStringContainsString('extra-index-2', $tableCreator->getSql());
    }

    public function testRealCreationOfTypeMain()
    {
        $tableCreator = new TableCreator('table-creator-main-extra');
        $tableCreator->setExtraFields([
            "`number_field` int(11) NOT NULL DEFAULT 0",
            "`string_field` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''",
        ]);
        $tableCreator->setExtraIndexes([
            "KEY `single-key` (`number_field`)",
            "KEY `union-key` (`number_field`, `string_field`)",
        ]);
        $tableCreator->execute();
        $this->assertTrue(true);
    }
}
