<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

use think\Exception;

class Validate extends FileCommon
{
    protected $rules;
    protected $messages;
    protected $scenes;
    protected $fieldsData;

    public function __construct()
    {
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
        parent::__construct();
    }

    private function buildRules()
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

    private function getRulesText(): string
    {
        $this->buildRules();
        $ruleText = '';
        foreach ($this->rules as $ruleKey => $ruleValue) {
            $ruleText .= "        '" . strtr($ruleKey, [$this->tableName . '@' => '']) . "' => '" . $ruleValue . "',\n";
        }
        return substr($ruleText, 0, -1);
    }

    private function buildMessages()
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

    public function getMessages(array $fieldsData)
    {
        $this->fieldsData = $fieldsData;
        $this->buildRules();
        $this->buildMessages();
        return $this->messages;
    }

    private function getMessagesText(): string
    {
        $this->buildMessages();
        $messageText = '';
        foreach ($this->messages as $msgKey => $msgValue) {
            if (strpos($msgKey, ':')) {
                $msgKey = substr($msgKey, 0, strpos($msgKey, ':'));
            }
            $messageText .= "        '" . $msgKey . "' => '" . $msgValue . "',\n";
        }
        return substr($messageText, 0, -1);
    }

    private function buildScenes()
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

    private function getScenesTextArray()
    {
        $this->buildScenes();
        $saveText = $this->scenes['save'] ? '\'' . implode('\', \'', $this->scenes['save']) . '\'' : '';
        $updateText = $this->scenes['update'] ? '\'' . implode('\', \'', $this->scenes['update']) . '\'' : '';
        $homeText = $this->scenes['home'] ? '\'' . implode('\', \'', $this->scenes['home']) . '\'' : '';
        $excludeText = '';
        foreach ($this->scenes['homeExclude'] as $exclude) {
            $excludeText .= "\n" . '            ->remove(\'' . $exclude . '\', \'require\')';
        }
        return [$saveText, $updateText, $homeText, $excludeText];
    }

    public function createValidateFile(array $fieldsData)
    {
        $this->fieldsData = $fieldsData;

        $targetPath = createPath($this->appPath, 'api', 'validate', $this->modelName) . '.php';
        $sourcePath = createPath($this->stubPath, 'Validate', 'default') . '.stub';

        $ruleText = $this->getRulesText();
        $messageText = $this->getMessagesText();
        list($sceneSaveText, $sceneUpdateText, $sceneHomeText, $sceneHomeExcludeText) = $this->getScenesTextArray();

        $replaceCondition = [
            '{%modelName%}' => $this->modelName,
            '{%ruleText%}' => $ruleText,
            '{%messageText%}' => $messageText,
            '{%sceneSaveText%}' => $sceneSaveText,
            '{%sceneUpdateText%}' => $sceneUpdateText,
            '{%sceneHomeText%}' => $sceneHomeText,
            '{%sceneHomeExcludeText%}' => $sceneHomeExcludeText,
        ];

        try {
            $this->replaceAndWrite($sourcePath, $targetPath, function ($content) use ($replaceCondition) {
                return strtr($content, $replaceCondition);
            });
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function removeValidateFile()
    {
        $targetPath = createPath($this->appPath, 'api', 'validate', $this->modelName) . '.php';
        $this->fileSystem->remove($targetPath);
    }
}
