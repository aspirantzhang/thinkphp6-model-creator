<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

use think\facade\Db as ThinkDb;
use think\Exception;

class DbTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        ThinkDb::execute('DROP TABLE IF EXISTS `auth_rule`, `auth_rule_i18n`, `menu`, `menu_i18n`, `auth_group_rule`, `unit-test`, `unit-test_i18n`, `unit-test-2`, `unit-test-2_i18n`;');
        ThinkDb::execute(<<<END
CREATE TABLE `auth_rule` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `parent_id` int(11) unsigned NOT NULL DEFAULT 0,
 `rule_path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
 `type` tinyint(1) NOT NULL DEFAULT 1,
 `condition` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
 `create_time` datetime NOT NULL,
 `update_time` datetime NOT NULL,
 `delete_time` datetime DEFAULT NULL,
 `status` tinyint(1) NOT NULL DEFAULT 1,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
END
        );
        ThinkDb::execute(<<<END
CREATE TABLE `auth_rule_i18n` (
 `_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `original_id` int(11) unsigned NOT NULL,
 `lang_code` char(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
 `rule_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
 `translate_time` datetime DEFAULT NULL,
 PRIMARY KEY (`_id`),
 UNIQUE KEY `original_id` (`original_id`,`lang_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
END
        );
        ThinkDb::execute(<<<END
CREATE TABLE `menu` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `parent_id` int(11) unsigned NOT NULL DEFAULT 0,
 `path` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
 `icon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
 `hide_in_menu` tinyint(1) NOT NULL DEFAULT 0,
 `hide_children_in_menu` tinyint(1) NOT NULL DEFAULT 0,
 `flat_menu` tinyint(1) NOT NULL DEFAULT 0,
 `create_time` datetime NOT NULL,
 `update_time` datetime NOT NULL,
 `delete_time` datetime DEFAULT NULL,
 `status` tinyint(1) NOT NULL DEFAULT 1,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
END
        );
        ThinkDb::execute(<<<END
CREATE TABLE `menu_i18n` (
 `_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `original_id` int(11) unsigned NOT NULL,
 `lang_code` char(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
 `menu_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
 `translate_time` datetime DEFAULT NULL,
 PRIMARY KEY (`_id`),
 UNIQUE KEY `original_id` (`original_id`,`lang_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
END
        );
        ThinkDb::execute(<<<END
CREATE TABLE `auth_group_rule` (
 `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `group_id` int(11) unsigned NOT NULL,
 `rule_id` int(11) unsigned NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `group_rule_id` (`group_id`,`rule_id`),
 KEY `group_id` (`group_id`),
 KEY `rule_id` (`rule_id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
END
        );
    }
    public function testCreateModelTableSuccessfully()
    {
        try {
            (new Db())->createModelTable('unit-test');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    /**
    * @depends testCreateModelTableSuccessfully
    */
    public function testCreateModelTableFailed()
    {
        try {
            (new Db())->createModelTable('unit-test');
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), 'create model table failed:unit-test');
            return;
        }
        $this->fail();
    }

    public function testRemoveModelTableSuccessfully()
    {
        try {
            (new Db())->removeModelTable('unit-test');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function testCreateRuleSuccessfully()
    {
        try {
            $id = (new Db())->createRule('Unit Test', 'en-us');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return $id;
    }
    /**
    * @depends testCreateRuleSuccessfully
    */
    public function testCreateChildrenRulesSuccessfully($id)
    {
        try {
            (new Db())->createChildrenRules($id, 'en-us', 'unit-test', 'Unit Test');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return $id;
    }
    /**
    * @depends testCreateChildrenRulesSuccessfully
    */
    public function testRemoveRuleSuccessfully($id)
    {
        try {
            (new Db())->removeRules($id);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function testAddRulesToGroupSuccessfully()
    {
        try {
            (new Db())->addRulesToGroup([100,200], 1);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function testCreateMenuSuccessfully()
    {
        try {
            $id = (new Db())->createMenu('Unit Test', 'en-us', 'testPath');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return $id;
    }
    /**
    * @depends testCreateMenuSuccessfully
    */
    public function testCreateChildrenMenusSuccessfully($id)
    {
        try {
            (new Db())->createChildrenMenus($id, 'en-us', 'unit-test', 'Unit Test');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return $id;
    }
    /**
    * @depends testCreateChildrenMenusSuccessfully
    */
    public function testRemoveMenuSuccessfully($id)
    {
        try {
            (new Db())->removeMenus($id);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    /**
    * @depends testRemoveMenuSuccessfully
    */
    public function testCreateModelSuccessfully()
    {
        try {
            $modelData = ModelCreator::db('unit-test-2', 'Unit Test 2', 'zh-cn')->createModel();
            $this->assertTrue(true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return $modelData;
    }
    /**
    * @depends testCreateModelSuccessfully
    */
    public function testCreateModelFailed($modelData)
    {
        try {
            ModelCreator::db('unit-test-2', 'Unit Test 2', 'zh-cn')->createModel();
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), 'create model table failed:unit-test-2');
            return $modelData;
        }
        $this->fail();
    }
    /**
    * @depends testCreateModelFailed
    */
    public function testRemoveModelSuccessfully($modelData)
    {
        try {
            ModelCreator::db('unit-test-2', 'Unit Test 2', 'zh-cn')->removeModel($modelData['topRuleId'], $modelData['topMenuId']);
            $this->assertTrue(true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
    /**
    * @depends testRemoveModelSuccessfully
    */
    public function testCreateRuleFailed()
    {
        ThinkDb::execute('DROP TABLE IF EXISTS `auth_rule`, `auth_rule_i18n`;');
        try {
            (new Db())->createRule('Unit Test 3', 'en-us');
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), 'failed to create rule:Unit Test 3');
            return;
        }
        $this->fail();
    }

    /**
    * @depends testCreateRuleFailed
    */
    public function testCreateMenuFailed()
    {
        ThinkDb::execute('DROP TABLE IF EXISTS `menu`, `menu_i18n`;');
        try {
            (new Db())->createMenu('Unit Test 4', 'en-us', 'testPath');
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), 'failed to create menu:Unit Test 4');
            return;
        }
        $this->fail();
    }

    /**
    * @depends testCreateRuleFailed
    */
    public function testAddRulesToGroupFailed()
    {
        ThinkDb::execute('DROP TABLE IF EXISTS `auth_group_rule`;');
        try {
            (new Db())->addRulesToGroup([100,200], 1);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), 'failed to add rules to group:');
            return;
        }
        $this->fail();
    }
    /**
    * @depends testCreateRuleFailed
    */
    public function testRemoveModelFailed()
    {
        try {
            ModelCreator::db('not-exist', '', 'zh-cn')->removeModel(0, 0);
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), 'failed to remove rules:');
            return;
        }
        $this->fail();
    }
}
