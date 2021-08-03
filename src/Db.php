<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

use aspirantzhang\octopusModelCreator\lib\db\Table;
use aspirantzhang\octopusModelCreator\lib\db\Rule;
use aspirantzhang\octopusModelCreator\lib\db\GroupRule;
use aspirantzhang\octopusModelCreator\lib\db\Menu;
use aspirantzhang\octopusModelCreator\lib\db\Field;

class Db
{
    protected $tableName;
    protected $modelTitle;

    public function init(string $tableName, string $modelTitle)
    {
        $this->tableName = $tableName;
        $this->modelTitle = $modelTitle;
        return $this;
    }

    public function create()
    {
        try {
            (new Table())->init($this->tableName, $this->modelTitle)->createModelTable();
            $topRuleId = (new Rule())->init($this->tableName, $this->modelTitle)->createRule();
            $childrenRuleIds = (new Rule())->init($this->tableName, $this->modelTitle)->createChildrenRules($topRuleId);
            (new GroupRule())->addRulesToGroup([$topRuleId, ...$childrenRuleIds]);
            $topMenuPath = '/basic-list/api/' . $this->tableName;
            $topMenuId = (new Menu())->init($this->tableName, $this->modelTitle)->createMenu($topMenuPath);
            $childrenMenuIds = (new Menu())->init($this->tableName, $this->modelTitle)->createChildrenMenus($topMenuId);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

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
            (new Field())->init($this->tableName, $this->modelTitle)->fieldsHandler($fieldsData, $mainTableFields, $reservedFields);
            if (!empty($i18nTableFields)) {
                (new Field())->init($this->tableName . '_i18n', $this->modelTitle)->fieldsHandler($fieldsData, $i18nTableFields, $reservedFields);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function remove(int $ruleId, int $menuId)
    {
        try {
            (new Rule())->removeRules($ruleId);
            (new Menu())->removeMenus($menuId);
            (new Table())->init($this->tableName, $this->modelTitle)->removeModelTable();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
