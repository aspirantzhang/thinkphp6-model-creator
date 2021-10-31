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
            'parent_id' => 'number|checkParentId',
            'revisionId' => 'require|number',
        ];
        $this->scenes = [
            'save' => ['create_time', 'status'],
            'update' => ['id', 'create_time', 'status'],
            'read' => ['id'],
            'delete' => ['ids'],
            'restore' => ['ids'],
            'i18n_read' => ['id'],
            'i18n_update' => ['id'],
            'revision_home' => ['page', 'per_page'],
            'revision_restore' => ['revisionId'],
            'revision_read' => [''],
            'add' => [''],
            'home' => [],
            'homeExclude' => []
        ];
        parent::__construct();
    }

    private function buildRules()
    {
        /**
        *  'news@nickname' => 'require|length:0,32'
        *   fullFieldName  => $ruleConditionSetString
        *  ruleCondition = ruleName:ruleOption
        */
        foreach ($this->fieldsData as $field) {
            $fieldName = $field['name'];
            if (in_array('parent_id', array_keys($this->rules))) {
                continue;
            }
            if (!empty($field['settings']['validate'])) {
                $ruleConditionSetString = '';
                foreach ($field['settings']['validate'] as $ruleName) {
                    switch ($ruleName) {
                        case 'length':
                            $min = $field['settings']['options']['length']['min'] ?? 0;
                            $max = $field['settings']['options']['length']['max'] ?? 32;
                            $ruleConditionSetString .= $ruleName . ':' . (int)$min . ',' . (int)$max . '|';
                            break;
                        default:
                            $ruleConditionSetString .= $ruleName . '|';
                            break;
                    }
                }
                // delete last symbol '|'
                $ruleConditionSetString = substr($ruleConditionSetString, 0, -1);
                // +prefix
                $fieldName = $this->tableName . '@' . $fieldName;
                $this->rules[$fieldName] = $ruleConditionSetString;
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
        /**
         * $this->rule : news@nickname => require|length:0,32
         * extract rule: nickname.require + nickname.length:0,32
         *  nickname.length  =>  news@nickname#length:0,32
         *      $fieldRule   =>  $messageCondition
         * fieldRule = fieldName.ruleName
         * messageCondition = fullFieldName#ruleCondition
         */
        foreach ($this->rules as $fullFieldName => $ruleConditionSet) {
            $fieldName = strtr($fullFieldName, [$this->tableName . '@' => '']);
            if (strpos($ruleConditionSet, '|')) {
                // have multiple condition
                $ruleConditionArray = explode('|', $ruleConditionSet);
                foreach ($ruleConditionArray as $ruleCondition) {
                    $this->messages[$fieldName . '.' . $ruleCondition] = $fullFieldName . '#' . $ruleCondition;
                }
            } else {
                $this->messages[$fieldName . '.' . $ruleConditionSet] = $fullFieldName . '#' . $ruleConditionSet;
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

    private function getScenesTextArray(): array
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
        $sourcePath = $this->getStubPath('Validate');

        $ruleText = $this->getRulesText();
        $messageText = $this->getMessagesText();
        list($sceneSaveText, $sceneUpdateText, $sceneHomeText, $sceneHomeExcludeText) = $this->getScenesTextArray();

        $replaceCondition = [
            '{{ modelName }}' => $this->modelName,
            '{{ ruleText }}' => $ruleText,
            '{{ messageText }}' => $messageText,
            '{{ sceneSaveText }}' => $sceneSaveText,
            '{{ sceneUpdateText }}' => $sceneUpdateText,
            '{{ sceneHomeText }}' => $sceneHomeText,
            '{{ sceneHomeExcludeText }}' => $sceneHomeExcludeText,
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
