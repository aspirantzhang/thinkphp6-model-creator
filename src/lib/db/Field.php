<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

use think\facade\Db;

class Field extends DbCommon
{
    private function getExistingFields()
    {
        $existingFields = [];
        $columnsQuery = Db::query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = :tableName;", ['tableName' => $this->tableName]);
        if ($columnsQuery) {
            $existingFields = extractValues($columnsQuery, 'COLUMN_NAME');
        }
        return $existingFields;
    }
 
    public function fieldsHandler(array $fieldsData, array $processedFields, array $reservedFields)
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
            $method = 'DROP COLUMN';
            if (!in_array($field, $reservedFields)) {
                $statements[] = " $method `$field`";
            }
        }

        $alterTableSql = 'ALTER TABLE `' . $this->tableName . '` ' . implode(',', $statements) . ';';

        try {
            Db::query($alterTableSql);
        } catch (\Exception $e) {
            throw new \Exception(__('change table structure failed', ['tableName' => $this->tableName]));
        }
    }
}
