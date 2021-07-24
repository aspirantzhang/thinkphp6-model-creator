<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

use think\facade\Db as ThinkDb;

class Db
{
    protected $tableName;
    protected $modelTitle;
    protected $currentLang;

    public function init(string $tableName, string $modelTitle, string $currentLang): Db
    {
        $this->tableName = $tableName;
        $this->modelTitle = $modelTitle;
        $this->currentLang = $currentLang;
        return $this;
    }

    public function createModel()
    {
        try {
            $this->createModelTable($this->tableName);
            $topRuleId = $this->createRule($this->modelTitle, $this->currentLang);
            $childrenRuleIds = $this->createChildrenRules($topRuleId, $this->currentLang, $this->tableName, $this->modelTitle);
            $this->addRulesToGroup([$topRuleId, ...$childrenRuleIds]);
            $topMenuId = $this->createMenu($this->modelTitle . __('list'), $this->currentLang, '/basic-list/api/' . $this->tableName);
            $childrenMenuIds = $this->createChildrenMenus($topMenuId, $this->currentLang, $this->tableName, $this->modelTitle);
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

    public function removeModel(int $ruleId, int $menuId)
    {
        try {
            $this->removeModelTable($this->tableName);
            $this->removeRules($ruleId);
            $this->removeMenus($menuId);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function createModelTable(string $tableName)
    {
        try {
            ThinkDb::execute("CREATE TABLE `$tableName` ( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT , `create_time` DATETIME NOT NULL , `update_time` DATETIME NOT NULL , `delete_time` DATETIME NULL DEFAULT NULL , `status` TINYINT(1) NOT NULL DEFAULT '1' , PRIMARY KEY (`id`)) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;");
            $i18nTable = $tableName . '_i18n';
            ThinkDb::execute("CREATE TABLE `$i18nTable` ( `_id` int unsigned NOT NULL AUTO_INCREMENT , `original_id` int unsigned NOT NULL , `lang_code` char(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '', `translate_time` DATETIME DEFAULT NULL, PRIMARY KEY (`_id`), UNIQUE KEY `original_id` (`original_id`,`lang_code`)) ENGINE = InnoDB CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;");
        } catch (\Exception $e) {
            throw new \Exception(__('create model table failed', ['tableName' => $tableName]));
        }
    }

    public function removeModelTable(string $tableName)
    {
        try {
            $i18nTable = $tableName . '_i18n';
            ThinkDb::execute("DROP TABLE IF EXISTS `$tableName`, `$i18nTable`;");
        } catch (\Exception $e) {
            $this->error = __('remove model table failed', ['tableName' => $tableName]);
        }
    }

    public function createRule(string $ruleTitle, string $lang, int $parentId = 0, string $rulePath = '')
    {
        $currentTime = date("Y-m-d H:i:s");
        try {
            $ruleId = ThinkDb::name('auth_rule')->insertGetId([
                'parent_id' => $parentId,
                'rule_path' => $rulePath,
                'create_time' => $currentTime,
                'update_time' => $currentTime,
            ]);
            ThinkDb::name('auth_rule_i18n')->insert([
                'original_id' => $ruleId,
                'lang_code' => $lang,
                'rule_title' => $ruleTitle,
                'translate_time' => $currentTime
            ]);
        } catch (\Exception $e) {
            throw new \Exception(__('failed to create rule', ['ruleTitle' => $ruleTitle]));
        }
        return (int)$ruleId;
    }

    public function removeRules(int $id)
    {
        try {
            $allRulesData = ThinkDb::table('auth_rule')->where('status', 1)->select()->toArray();
            $allIds = array_merge([$id], searchDescendantValueAggregation('id', 'id', $id, arrayToTree($allRulesData)));
            ThinkDb::table('auth_rule')->whereIn('id', $allIds)->delete();
            ThinkDb::table('auth_rule_i18n')->whereIn('original_id', $allIds)->delete();
            ThinkDb::table('auth_group_rule')->whereIn('rule_id', $allIds)->delete();
        } catch (\Exception $e) {
            throw new \Exception(__('failed to remove rules'));
        }
    }

    public function createChildrenRules(int $parentRuleId, string $lang, string $tableName, string $modelTitle)
    {
        $childrenRules = [
            ['rule_title' => $modelTitle . __('rule_title_home'), 'rule_path' => 'api/' . $tableName . '/home'],
            ['rule_title' => $modelTitle . __('rule_title_add'), 'rule_path' => 'api/' . $tableName . '/add'],
            ['rule_title' => $modelTitle . __('rule_title_save'), 'rule_path' => 'api/' . $tableName . '/save'],
            ['rule_title' => $modelTitle . __('rule_title_read'), 'rule_path' => 'api/' . $tableName . '/read'],
            ['rule_title' => $modelTitle . __('rule_title_update'), 'rule_path' => 'api/' . $tableName . '/update'],
            ['rule_title' => $modelTitle . __('rule_title_delete'), 'rule_path' => 'api/' . $tableName . '/delete'],
            ['rule_title' => $modelTitle . __('rule_title_restore'), 'rule_path' => 'api/' . $tableName . '/restore'],
            ['rule_title' => $modelTitle . __('rule_title_i18n'), 'rule_path' => 'api/' . $tableName . '/i18n'],
            ['rule_title' => $modelTitle . __('rule_title_i18nUpdate'), 'rule_path' => 'api/' . $tableName . '/i18n_update'],
        ];
        $childrenIds = [];
        try {
            foreach ($childrenRules as $rule) {
                $childrenIds[] = $this->createRule($rule['rule_title'], $lang, $parentRuleId, $rule['rule_path']);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return $childrenIds;
    }

    public function addRulesToGroup(array $ruleIds, int $groupId = 1)
    {
        $data = [];
        foreach ($ruleIds as $ruleId) {
            $data[] = ['group_id' => $groupId, 'rule_id' => $ruleId];
        }
        try {
            ThinkDb::name('auth_group_rule')->insertAll($data);
        } catch (\Exception $e) {
            throw new \Exception(__('failed to add rules to group'));
        }
    }

    public function createMenu(string $menuTitle, string $lang, string $menuPath, int $parentId = 0, $addition = [])
    {
        $currentTime = date("Y-m-d H:i:s");
        try {
            $menuId = ThinkDb::name('menu')->insertGetId(array_merge([
                'parent_id' => $parentId,
                'icon' => 'icon-project',
                'path' => $menuPath,
                'create_time' => $currentTime,
                'update_time' => $currentTime,
            ], $addition));
            ThinkDb::name('menu_i18n')->insert([
                'original_id' => $menuId,
                'lang_code' => $lang,
                'menu_title' => $menuTitle,
                'translate_time' => $currentTime
            ]);
        } catch (\Exception $e) {
            throw new \Exception(__('failed to create menu', ['menuTitle' => $menuTitle]));
        }
        return (int)$menuId;
    }

    public function removeMenus(int $id)
    {
        try {
            $allRulesData = ThinkDb::table('menu')->where('status', 1)->select()->toArray();
            $allIds = array_merge([$id], searchDescendantValueAggregation('id', 'id', $id, arrayToTree($allRulesData)));
            ThinkDb::table('menu')->whereIn('id', $allIds)->delete();
            ThinkDb::table('menu_i18n')->whereIn('original_id', $allIds)->delete();
        } catch (\Exception $e) {
            throw new \Exception(__('failed to remove menus'));
        }
    }

    public function createChildrenMenus(int $parentMenuId, string $lang, string $tableName, string $modelTitle)
    {
        $childrenMenus = [
            ['menu_title' => $modelTitle . __('add'), 'path' => '/basic-list/api/' . $tableName . '/add', 'hide_in_menu' => 1],
            ['menu_title' => $modelTitle . __('edit'), 'path' => '/basic-list/api/' . $tableName . '/:id', 'hide_in_menu' => 1],
        ];
        $childrenIds = [];
        try {
            foreach ($childrenMenus as $menu) {
                $addition = $menu;
                unset($addition['menu_title'], $addition['path']);
                $childrenIds[] = $this->createMenu($menu['menu_title'], $lang, $menu['path'], $parentMenuId, $addition);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return $childrenIds;
    }

    private function getExistingFields(string $tableName)
    {
        $existingFields = [];
        $columnsQuery = ThinkDb::query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = :tableName;", ['tableName' => $tableName]);
        if ($columnsQuery) {
            $existingFields = extractValues($columnsQuery, 'COLUMN_NAME');
        }
        return $existingFields;
    }
 
    public function fieldsHandler(array $processedFields, array $fieldsData, array $reservedFields)
    {
        $existingFields = $this->getExistingFields($this->tableName);
        // group by types
        $delete = array_diff($existingFields, $processedFields);
        $add = array_diff($processedFields, $existingFields);
        $change = array_intersect($processedFields, $existingFields);

        $statements = [];
        foreach ($fieldsData as $field) {
            $type = '';
            $typeAddon = '';
            $default = '';
            
            switch ($field['type']) {
                case 'longtext':
                    $type = 'LONGTEXT';
                    $typeAddon = '';
                    $default = '';
                    break;
                case 'number':
                    $type = 'INT';
                    $typeAddon = ' UNSIGNED';
                    $default = 'DEFAULT 0';
                    break;
                case 'datetime':
                    $type = 'DATETIME';
                    $typeAddon = '';
                    break;
                case 'tag':
                case 'switch':
                    $type = 'TINYINT';
                    $typeAddon = '(1)';
                    $default = 'DEFAULT 1';
                    break;
                default:
                    $type = 'VARCHAR';
                    $typeAddon = '(255)';
                    $default = 'DEFAULT \'\'';
                    break;
            }

            if (in_array($field['name'], $add)) {
                $method = 'ADD';
                $statements[] = " $method `${field['name']}` $type$typeAddon NOT NULL $default";
            }

            if (in_array($field['name'], $change)) {
                $method = 'CHANGE';
                $statements[] = " $method `${field['name']}` `${field['name']}` $type$typeAddon NOT NULL $default";
            }
        }

        foreach ($delete as $field) {
            $method = 'DROP IF EXISTS';
            if (!in_array($field, $reservedFields)) {
                $statements[] = " $method `$field`";
            }
        }

        $alterTableSql = 'ALTER TABLE `' . $this->tableName . '` ' . implode(',', $statements) . ';';

        try {
            ThinkDb::query($alterTableSql);
        } catch (\Exception $e) {
            throw new \Exception(__('change table structure failed', ['tableName' => $this->tableName]));
        }
    }
}
