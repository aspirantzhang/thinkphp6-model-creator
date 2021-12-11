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
use aspirantzhang\octopusModelCreator\lib\db\InitModelData;

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

        return (new InitModelData($data['table_name'], $data['type']))->getData();
    }

    public function integrateWithBuiltInFields(array $model)
    {
        $basicTabs = [
            [
                'name' => 'title',
                'title' => 'Title',
                'type' => 'input',
                'allowTranslate' => '1',
            ],
            [
                'name' => 'pathname',
                'title' => 'Path',
                'type' => 'input',
            ],
        ];
        if (isset($model['data']['fields']['tabs']) && !empty($model['data']['fields']['tabs'])) {
            $model['data']['fields']['tabs']['basic'] = [...$basicTabs, ...$model['data']['fields']['tabs']['basic']];
        } else {
            $model['data']['fields']['tabs']['basic'] = $basicTabs;
        }

        $basicSidebars = [
            [
                'name' => 'create_time',
                'title' => 'Create Time',
                'type' => 'datetime',
            ],
            [
                'name' => 'update_time',
                'title' => 'Update Time',
                'type' => 'datetime',
            ],
            [
                'name' => 'status',
                'title' => 'Status',
                'type' => 'switch',
            ],
            [
                'name' => 'list_order',
                'title' => 'Order',
                'type' => 'number',
            ],
        ];
        if (isset($model['data']['fields']['sidebars']) && !empty($model['data']['fields']['sidebars'])) {
            $model['data']['fields']['sidebars']['basic'] = [...$basicSidebars, ...$model['data']['fields']['sidebars']['basic']];
        } else {
            $model['data']['fields']['sidebars']['basic'] = $basicSidebars;
        }

        return $model;
    }
}
