<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

use think\facade\Db;
use think\Exception;
use aspirantzhang\octopusModelCreator\ModelCreator;

class Table extends DbCommon
{
    private function createTypeMain()
    {
        (new TableCreator($this->tableName))->execute();
        (new TableCreator($this->tableName . '_i18n', 'i18n'))->execute();
    }

    private function createTypeCategory(array $addon = [])
    {
        if (!isset($addon['mainTableName']) || empty($addon['mainTableName'])) {
            throw new Exception(__('missing required data: mainTableName'));
        }
        // main table
        $mainTableName = $addon['mainTableName'];
        $mainTableData = Db::table('model')->json(['data'])->where('table_name', $mainTableName)->find();
        if (
            $mainTableData &&
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

        // category table
        (new TableCreator($this->tableName))
            ->setExtraFields(['`parent_id` int(11) unsigned NOT NULL DEFAULT 0'])
            ->setExtraIndexes(['KEY `parent_id` (`parent_id`)'])
            ->execute();
        // add category i18n table
        (new TableCreator($this->tableName . '_i18n', 'i18n'))
            ->execute();
        // add pivot table
        $pivotTableName = 'pivot_' . ($addon['mainTableName'] ?? $this->tableName) . '_category';
        (new TableCreator($pivotTableName, 'custom'))
            ->setSql("CREATE TABLE `$pivotTableName` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `item_id` int(11) unsigned NOT NULL,
                `category_id` int(11) unsigned NOT NULL,
                PRIMARY KEY (`id`),
                KEY `item_id` (`item_id`),
                KEY `category_id` (`category_id`),
                KEY `item_category_id` (`item_id`,`category_id`)
            ) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;")
            ->execute();
    }

    public function createModelTable(array $addon = [])
    {
        switch ($this->modelType) {
            case 'category':
                $this->createTypeCategory($addon);
                break;

            default:
                $this->createTypeMain();
                break;
        }
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
