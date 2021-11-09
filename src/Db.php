<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

use think\Exception;
use think\facade\Db as ThinkDb;
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

    public function create()
    {
        $config = $this->getConfig();
        if ($config['type'] === 'category') {
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

    public function update(array $fieldsData, array $mainTableFields, array $reservedFields, array $i18nTableFields = [])
    {
        try {
            (new Field())->init($this->getConfig())->fieldsHandler($fieldsData, $mainTableFields, $reservedFields);
            if (!empty($i18nTableFields)) {
                (new Field())->init($this->getConfig('i18n'))->fieldsHandler($fieldsData, $i18nTableFields, $reservedFields);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function remove(int $ruleId, int $menuId)
    {
        try {
            (new Rule())->removeRules($ruleId);
            (new Menu())->removeMenus($menuId);
            (new Table())->init($this->getConfig())->removeModelTable();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
