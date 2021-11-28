<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

use think\Exception;
use think\facade\Db as ThinkDb;
use think\facade\Config;
use aspirantzhang\octopusModelCreator\Helper;
use aspirantzhang\octopusModelCreator\lib\db\Table;
use aspirantzhang\octopusModelCreator\lib\db\Rule;
use aspirantzhang\octopusModelCreator\lib\db\GroupRule;
use aspirantzhang\octopusModelCreator\lib\db\Menu;
use aspirantzhang\octopusModelCreator\lib\db\Field;

class Db
{
    protected $config;

    private function getConfig($type = 'main')
    {
        $config = $this->config;
        $config['type'] = $config['type'] ?? 'main';
        if ($type === 'i18n') {
            $config['name'] = $config['name'] . '_i18n';
        }
        return $config;
    }

    public function config(array $config)
    {
        if (
            !isset($config['name']) ||
            empty($config['name']) ||
            !isset($config['title']) ||
            empty($config['title'])
        ) {
            throw new Exception(__('missing required config name and title'));
        }
        $this->config = $config;
        return $this;
    }

    private function getMainTableInfo(int $id)
    {
        $mainTable = ThinkDb::name('model')
            ->alias('o')
            ->where('o.id', $id)
            ->leftJoin('model_i18n i', 'o.id = i.original_id')
            ->find();
        if ($mainTable === null) {
            throw new Exception(__('can not find main table'));
        }
        return $mainTable;
    }

    public function checkCategoryTypeConfig()
    {
        $config = $this->getConfig();
        if (
            !isset($config['parentId']) ||
            empty($config['parentId'])
        ) {
            throw new Exception(__('missing required config parentId'));
        }
    }

    public function create()
    {
        $config = $this->getConfig();
        if ($config['type'] === 'category') {
            $this->checkCategoryTypeConfig();
            // get main table info using parent id
            $mainTable = $this->getMainTableInfo((int)$config['parentId']);
            (new Table())->init($config)->createModelTable(['mainTableName' => $mainTable['table_name']]);
        } else {
            (new Table())->init($config)->createModelTable();
        }
        $topRuleId = (new Rule())->init($config)->createRule();
        $childrenRuleIds = (new Rule())->init($config)->createChildrenRules($topRuleId);
        (new GroupRule())->addRulesToGroup([$topRuleId, ...$childrenRuleIds]);
        $topMenuPath = '/basic-list/api/' . $config['name'];
        $topMenuId = (new Menu())->init($config)->createMenu($topMenuPath);
        $childrenMenuIds = (new Menu())->init($config)->createChildrenMenus($topMenuId);

        return [
            'topRuleId' => $topRuleId,
            'childrenRuleIds' => $childrenRuleIds,
            'topMenuId' => $topMenuId,
            'childrenMenuIds' => $childrenMenuIds
        ];
    }

    private function extractTranslateFields(array $allFields): array
    {
        $result = [];
        foreach ($allFields as $field) {
            // only input/textarea/textEditor can be translated
            if (
                isset($field['type']) &&
                ($field['type'] === 'input' || $field['type'] === 'textarea' || $field['type'] === 'textEditor') &&
                ($field['allowTranslate'] ?? false)
            ) {
                // cannot be marked as 'editDisabled' and 'translate' ATST
                if (
                    isset($field['settings']['display']) &&
                    in_array('editDisabled', $field['settings']['display'])
                ) {
                    throw new Exception(__('edit disabled fields cannot set as translate', ['fieldName' => $field['name']]));
                }
                array_push($result, $field['name']);
            }
        }
        return $result;
    }

    public function update(array $fieldsData)
    {
        $allFieldsArray = ModelCreator::helper()->extractAllFields($fieldsData);
        $allFieldNames = extractValues($allFieldsArray, 'name');
        (new Helper())->checkContainsMysqlReservedKeywords($allFieldNames);
        (new Helper())->checkContainsReservedFieldNames($allFieldNames);

        $reservedFields = Config::get('reserved.reserved_field');
        $i18nTableFields = $this->extractTranslateFields($allFieldsArray);
        $mainTableFields = array_diff($allFieldNames, $reservedFields, $i18nTableFields);

        (new Field())->init($this->getConfig())->fieldsHandler($allFieldsArray, $mainTableFields, $reservedFields);
        if (!empty($i18nTableFields)) {
            (new Field())->init($this->getConfig('i18n'))->fieldsHandler($allFieldsArray, $i18nTableFields, $reservedFields);
        }
    }

    public function remove(int $ruleId, int $menuId)
    {
        try {
            (new Rule())->removeRules($ruleId);
            (new Menu())->removeMenus($menuId);
            $config = $this->getConfig();
            if ($config['type'] === 'category') {
                $this->checkCategoryTypeConfig();
                // get main table info using parent id
                $mainTable = $this->getMainTableInfo((int)$config['parentId']);
                (new Table())->init($this->getConfig())->removeModelTable(['mainTableName' => $mainTable['table_name']]);
            } else {
                (new Table())->init($this->getConfig())->removeModelTable();
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function initModelDataField(array $data)
    {
        if (
            !isset($data['table_name']) ||
            empty($data['table_name']) ||
            !isset($data['type']) ||
            empty($data['type'])
        ) {
            throw new Exception(__('missing required data: table_name and type'));
        }
        $dataField = [];
        $dataField['layout']['tableName'] = strtolower($data['table_name']);
        // category type
        if ((int)$data['type'] === 2) {
            $parentField = [
                "name" => "parent_id",
                "title" => "Parent",
                "type" => "parent",
                "settings" => [ "validate" => ["checkParentId"] ],
                "allowHome" => "1",
                "allowRead" => "1",
                "allowSave" => "1",
                "allowUpdate" => "1"
            ];
            $dataField['fields']['sidebars']['parent'][] = $parentField;
        }
        return $dataField;
    }
}
