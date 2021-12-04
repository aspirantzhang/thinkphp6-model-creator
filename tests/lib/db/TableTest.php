<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

use think\Exception;
use think\facade\Db as ThinkDb;

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
        $this->expectExceptionMessage('create table failed: tableName=table-test');
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

    private function createCategoryModelTableForTesting()
    {
        ThinkDb::execute('DROP TABLE IF EXISTS `model`, `model_i18n`, `category_model`, `category_model_i18n`, `pivot_main_table_category`;');
        ThinkDb::execute(<<<END
CREATE TABLE `model` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`table_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
`type` tinyint(4) unsigned NOT NULL DEFAULT 1,
`parent_id` int(11) unsigned NOT NULL DEFAULT 0,
`data` text COLLATE utf8mb4_unicode_ci NOT NULL,
`rule_id` int(11) unsigned NOT NULL DEFAULT 0,
`menu_id` int(11) unsigned NOT NULL DEFAULT 0,
`create_time` datetime NOT NULL,
`update_time` datetime NOT NULL,
`delete_time` datetime DEFAULT NULL,
`status` tinyint(1) NOT NULL DEFAULT 1,
PRIMARY KEY (`id`),
UNIQUE KEY `table_name` (`table_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
END
        );
        ThinkDb::execute(<<<END
CREATE TABLE `model_i18n` (
    `_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `original_id` int(11) unsigned NOT NULL,
    `lang_code` char(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `model_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `translate_time` datetime DEFAULT NULL,
    PRIMARY KEY (`_id`),
    UNIQUE KEY `original_id` (`original_id`,`lang_code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
END
        );

        $currentTime = date('Y-m-d H:i:s');
        $mainTableDemo = $this->getDemo('news-main-table');
        ThinkDb::name('model')->insertAll([
            [
                'id' => 1,
                'table_name' => 'main_table',
                'type' => 1,
                'parent_id' => 0,
                'data' => json_encode($mainTableDemo),
                'create_time' => $currentTime,
                'update_time' => $currentTime,
            ],
        ]);

        ThinkDb::name('model_i18n')->insertAll([
            [
                '_id' => 1,
                'original_id' => 1,
                'lang_code' => 'en-us',
                'model_title' => 'Main Model',
            ],
        ]);
    }

    public function testCreateTypeCategory()
    {
        $this->createCategoryModelTableForTesting();
        $config = [
            'name' => 'category_model',
            'title' => 'Category Model',
            'type' => 'category',
            'parentId' => 1,
        ];
        (new Table())->init($config)->createModelTable([
            'mainTableName' => 'main_table'
        ]);
        $this->assertTrue(true);
    }
}
