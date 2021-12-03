<?php

namespace aspirantzhang\octopusModelCreator\lib\db;

use think\facade\Db;
use think\Exception;

class TableCreator
{
    private $tableName;
    private $type;
    private $sql;
    private $extraFields;
    private $extraIndexes;

    public function __construct(string $tableName, string $type = 'main')
    {
        if (empty($tableName)) {
            throw new Exception(__('missing table name when creating table'));
        }

        $this->tableName = $tableName;
        $this->type = $type;
    }

    public function getSql()
    {
        return $this->sql;
    }

    public function setExtraFields(array $fields)
    {
        $this->extraFields = $fields;
        return $this;
    }

    public function getExtraFields(): string
    {
        if (empty($this->extraFields)) {
            return '';
        }

        return implode(",\n        ", $this->extraFields) . ',';
    }

    public function setExtraIndexes(array $indexes)
    {
        $this->extraIndexes = $indexes;
        return $this;
    }

    public function getExtraIndexes()
    {
        if (empty($this->extraIndexes)) {
            return '';
        }
        return implode(",\n        ", $this->extraIndexes) . ',';
    }

    public function buildSql()
    {
        switch ($this->type) {
            case 'main':
                $this->buildTypeMain();
                break;
            case 'i18n':
                $this->buildTypeI18n();
                break;
            default:
                break;
        }
    }

    private function buildTypeMain()
    {
        $extraFields = $this->getExtraFields();
        $extraIndexes = $this->getExtraIndexes();
        $this->sql = "CREATE TABLE `$this->tableName` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `pathname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
        `list_order` int(11) NOT NULL DEFAULT 0,
        `create_time` DATETIME NOT NULL,
        `update_time` DATETIME NOT NULL,
        `delete_time` DATETIME NULL DEFAULT NULL,
        `status` TINYINT(1) NOT NULL DEFAULT '1',
        $extraFields
        PRIMARY KEY (`id`),
        $extraIndexes
        KEY `pathname_status` (`pathname`, `status`)
        ) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;";
    }

    private function buildTypeI18n()
    {
        $extraFields = $this->getExtraFields();
        $extraIndexes = $this->getExtraIndexes();
        $this->sql = "CREATE TABLE `$this->tableName` (
        `_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `original_id` int(11) unsigned NOT NULL,
        `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
        `lang_code` char(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
        `translate_time` datetime DEFAULT NULL,
        $extraFields
        PRIMARY KEY (`_id`),
        $extraIndexes
        UNIQUE KEY `original_lang` (`original_id`,`lang_code`)
        ) ENGINE = InnoDB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;";
    }

    public function execute()
    {
        $this->buildSql();
        try {
            Db::execute($this->getSql());
        } catch (Exception $e) {
            throw new Exception(__('create model table failed', ['tableName' => $this->tableName]));
        }
    }
}
