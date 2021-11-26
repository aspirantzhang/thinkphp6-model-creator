<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

use think\facade\Db;
use think\helper\Str;
use think\Exception;

class Table extends DbCommon
{
    private function createTypeMain()
    {
        try {
            Db::execute("CREATE TABLE `$this->tableName` ( `id` int(11) unsigned NOT NULL AUTO_INCREMENT, `create_time` DATETIME NOT NULL, `update_time` DATETIME NOT NULL, `delete_time` DATETIME NULL DEFAULT NULL, `status` TINYINT(1) NOT NULL DEFAULT '1', PRIMARY KEY (`id`)) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;");
            $i18nTable = $this->tableName . '_i18n';
            Db::execute("CREATE TABLE `$i18nTable` ( `_id` int(11) unsigned NOT NULL AUTO_INCREMENT, `original_id` int(11) unsigned NOT NULL, `lang_code` char(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '', `translate_time` DATETIME DEFAULT NULL, PRIMARY KEY (`_id`), UNIQUE KEY `original_id` (`original_id`,`lang_code`)) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;");
        } catch (Exception $e) {
            throw new Exception(__('create model table failed', ['tableName' => $this->tableName]));
        }
    }

    // TODO: check $addon['mainTableName'] required
    private function createTypeCategory(array $addon = [])
    {
        $mainTableName = $addon['mainTableName'];
        $mainTableData = Db::table('model')->json(['data'])->where('table_name', $mainTableName)->find();
        if (
            $mainTableData &&
            isset($mainTableData['data']['fields']['sidebars']) &&
            !isset($mainTableData['data']['fields']['sidebars']['category'])
        ) {
            $categoryField = [
                "name" => "category",
                "title" => "Category",
                "type" => "category",
                "settings" => [ "validate" => ["numberArray"] ],
                "allowHome" => "1",
                "allowRead" => "1",
                "allowSave" => "1",
                "allowUpdate" => "1"
            ];
            $mainTableData['data']['fields']['sidebars']['category'][] = $categoryField;
            try {
                Db::name('model')
                ->json(['data'])
                ->where('table_name', $mainTableName)
                ->update($mainTableData);
            } catch (Exception $e) {
                throw new Exception(__('failed to add category field to main table'));
            }
        }

        try {
            Db::execute("CREATE TABLE `$this->tableName` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `parent_id` int(11) unsigned NOT NULL DEFAULT 0,
                `create_time` DATETIME NOT NULL,
                `update_time` DATETIME NOT NULL,
                `delete_time` DATETIME NULL DEFAULT NULL,
                `status` tinyint(1) NOT NULL DEFAULT '1',
                PRIMARY KEY (`id`),
                KEY `parent_id` (`parent_id`)
            ) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;");
        } catch (Exception $e) {
            throw new Exception(__('create table failed', ['tableName' => $this->tableName]));
        }

        try {
            $i18nTable = $this->tableName . '_i18n';
            Db::execute("CREATE TABLE `$i18nTable` (
                `_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `original_id` int(11) unsigned NOT NULL,
                `lang_code` char(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                `translate_time` DATETIME DEFAULT NULL,
                `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
                PRIMARY KEY (`_id`),
                UNIQUE KEY `original_id` (`original_id`,`lang_code`)
            ) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;");
        } catch (Exception $e) {
            throw new Exception(__('create failed', ['tableName' => $i18nTable]));
        }

        try {
            $pivotTable = 'pivot_' . ($addon['mainTableName'] ?? $this->tableName) . '_category';
            Db::execute("CREATE TABLE `$pivotTable` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `item_id` int(11) unsigned NOT NULL,
                `category_id` int(11) unsigned NOT NULL,
                PRIMARY KEY (`id`),
                KEY `item_id` (`item_id`),
                KEY `category_id` (`category_id`),
                KEY `item_category_id` (`item_id`,`category_id`)
            ) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        } catch (Exception $e) {
            throw new Exception(__('create table failed', ['tableName' => $pivotTable]));
        }
    }

    public function createModelTable(array $addon = [])
    {
        $createMethodName = 'createType' . Str::studly($this->modelType);
        if (method_exists($this, $createMethodName)) {
            return $this->$createMethodName($addon);
        }
        throw new Exception(__('cannot find the method to create model tables', ['methodName' => $createMethodName]));
    }

    public function removeModelTable(array $addon = [])
    {
        try {
            $i18nTable = $this->tableName . '_i18n';
            Db::execute("DROP TABLE IF EXISTS `$this->tableName`, `$i18nTable`;");
            if ($this->modelType === 'category') {
                $pivotTable = 'pivot_' . ($addon['mainTableName'] ?? $this->tableName) . '_category';
                Db::execute("DROP TABLE IF EXISTS `$pivotTable`;");
            }
        } catch (Exception $e) {
            throw new Exception(__('remove model table failed', ['tableName' => $this->tableName]));
        }
    }
}
