<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

use think\Exception;
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

    public function create()
    {
        try {
            (new Table())->init($this->getConfig())->createModelTable();
            $topRuleId = (new Rule())->init($this->getConfig())->createRule();
            $childrenRuleIds = (new Rule())->init($this->getConfig())->createChildrenRules($topRuleId);
            (new GroupRule())->addRulesToGroup([$topRuleId, ...$childrenRuleIds]);
            $topMenuPath = '/basic-list/api/' . $this->getConfig()['name'];
            $topMenuId = (new Menu())->init($this->getConfig())->createMenu($topMenuPath);
            $childrenMenuIds = (new Menu())->init($this->getConfig())->createChildrenMenus($topMenuId);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
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
