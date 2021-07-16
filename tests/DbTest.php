<?php

declare(strict_types=1);

namespace aspirantzhang\thinkphp6ModelCreator;

use think\facade\Db as ThinkDb;
use think\Exception;

use function __;

class DbTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        ThinkDb::setConfig([
            // 默认数据连接标识
            'default'     => 'mysql',
            // 数据库连接信息
            'connections' => [
                'mysql' => [
                    // 数据库类型
                    'type'     => 'mysql',
                    // 主机地址
                    'hostname' => '127.0.0.1',
                    // 数据库名
                    'database' => 'model_creator',
                    // 用户名
                    'username' => 'root',
                    // 密码
                    'password' => 'dbpass',
                    // 数据库编码默认采用utf8
                    'charset'  => 'utf8',
                    // 数据库表前缀
                    'prefix'   => '',
                    // 是否需要断线重连
                    'break_reconnect' => false,
                    // 断线标识字符串
                    'break_match_str' => [],
                    // 数据库调试模式
                    'debug'    => false,
                ],
            ],
        ]);
        
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

    public function testCreateRuleSuccessfully()
    {
        try {
            (new Db())->createRule('Unit Test', 'en-us');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function testCreateChildrenRulesSuccessfully()
    {
        try {
            (new Db())->createChildrenRules(1, 'en-us', 'unit-test', 'Unit Test');
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
            (new Db())->createMenu('Unit Test', 'en-us', 'testPath');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function testCreateChildrenMenusSuccessfully()
    {
        try {
            (new Db())->createChildrenMenus(1, 'en-us', 'unit-test', 'Unit Test');
            $this->assertTrue(true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function testCreateModelSuccessfully()
    {
        try {
            ModelCreator::db('unit-test-2', 'Unit Test 2', 'zh-cn')->createModel();
            $this->assertTrue(true);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
