<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib;

class Validate
{
    protected $tableName;
    protected $fieldsData;
    protected $rules;
    protected $messages;
    protected $scenes;

    public function __construct(string $tableName, array $fieldsData)
    {
        $this->tableName = $tableName;
        $this->fieldsData = $fieldsData;
        $this->rules = [
            'id' => 'require|number',
            'ids' => 'require|numberArray',
            'status' => 'numberTag',
            'page' => 'number',
            'per_page' => 'number',
            'create_time' => 'require|dateTimeRange',
        ];
        $this->scenes = [
            'save' => ['create_time', 'status'],
            'update' => ['id', 'create_time', 'status'],
            'read' => ['id'],
            'delete' => ['ids'],
            'restore' => ['ids'],
            'i18n' => ['id'],
            'i18n_update' => ['id'],
            'add' => [''],
            'home' => [],
            'homeExclude' => []
        ];
    }

    protected function buildRules()
    {
        foreach ($this->fieldsData as $field) {
            $fieldName = $field['name'];
            $ruleString = '';
            if (!empty($field['settings']['validate'])) {
                foreach ($field['settings']['validate'] as $validateName) {
                    switch ($validateName) {
                        case 'length':
                            $min = $field['settings']['options']['length']['min'] ?? 0;
                            $max = $field['settings']['options']['length']['max'] ?? 32;
                            $ruleString .= $validateName . ':' . (int)$min . ',' . (int)$max . '|';
                            break;
                        default:
                            $ruleString .= $validateName . '|';
                            break;
                    }
                }
                $ruleString = substr($ruleString, 0, -1);
                // +prefix
                $ruleName = $this->tableName . '@' . $fieldName;
                $this->rules[$ruleName] = $ruleString;
            }
        }
    }

    protected function buildMessages()
    {
        foreach ($this->rules as $name => $rule) {
            $keyFieldName = strtr($name, [$this->tableName . '@' => '']);
            if (strpos($rule, '|')) {
                $ruleArr = explode('|', $rule);
                foreach ($ruleArr as $subRule) {
                    $this->messages[$keyFieldName . '.' . $subRule] = $name . '#' . $subRule;
                }
            } else {
                $this->messages[$keyFieldName . '.' . $rule] = $name . '#' . $rule;
            }
        }
    }

    protected function buildScenes()
    {
        foreach ($this->fieldsData as $field) {
            if (isset($field['settings']['validate']) && !empty($field['settings']['validate'])) {
                // home
                if ($field['allowHome'] ?? false) {
                    array_push($this->scenes['home'], $field['name']);
                    array_push($this->scenes['homeExclude'], $field['name']);
                }
                // save
                if ($field['allowSave'] ?? false) {
                    array_push($this->scenes['save'], $field['name']);
                }
                // update
                if ($field['allowUpdate'] ?? false) {
                    array_push($this->scenes['update'], $field['name']);
                }
            }
        }
    }

    public function getData()
    {
        $this->buildRules();
        $this->buildMessages();
        $this->buildScenes();
        return [
            'rules' => $this->rules,
            'messages' => $this->messages,
            'scenes' => $this->scenes
        ];
    }
}
