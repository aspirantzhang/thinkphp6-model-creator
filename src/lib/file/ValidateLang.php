<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

use think\Exception;
use think\facade\Lang;

class ValidateLang extends FileCommon
{
    private $builtInRules = [
        'id.require',
        'id.number',
        'ids.require',
        'ids.numberArray',
        'status.numberTag',
        'page.number',
        'per_page.number',
        'create_time.require',
        'create_time.dateTimeRange',
        'parent_id.number',
        'parent_id.checkParentId',
        'revisionId.require',
        'revisionId.number'
    ];
    /**
     * ATTENTION: field lang should be load in advance
     */
    private function getRuleText(string $fieldRule)
    {
        // fieldRule: news@nickname#length:0,32
        // extract field name: news.nickname
        $fieldName = substr($fieldRule, 0, strpos($fieldRule, '#'));
        $fieldName = strtr($fieldName, ['@' => '.']);

        $ruleName = substr($fieldRule, strpos($fieldRule, '#') + 1);
        $option = '';
        if (strpos($ruleName, ':')) {
            $ruleName = substr($ruleName, 0, strpos($ruleName, ':'));
            $option = substr($fieldRule, strpos($fieldRule, ':') + 1);
            if ($option) {
                // 0,32 -> 0 - 32
                $option = strtr($option, [',' => ' - ']);
            }
        }
        $ruleLang = $this->getDefaultLang('validate');
        if (isset($ruleLang['default.' . $ruleName])) {
            return strtr($ruleLang['default.' . $ruleName], ['{{ field }}' => '[' . __($fieldName) . ']', '{{ option }}' => '[' . $option . ']']);
        } else {
            return 'custom_rule_' . $ruleName;
        }
    }

    private function buildValidateData(array $fieldsData)
    {
        /**
         * from message
         * 'nickname.length' => 'news@nickname#length:0,32',
         * to messageI18n
         * 'news@nickname#length:0,32' => 'Nick Name length should be between 0 - 32.',
         */
        $validateMessages = (new Validate())->init($this->tableName, $this->modelTitle)->getMessages($fieldsData);
        // exclude built in field rule
        $fieldRules = array_diff_key($validateMessages, array_flip($this->builtInRules));
        $data = '';
        foreach ($fieldRules as $fieldRule) {
            $data .= '    \'' . $fieldRule . '\' => \'' . $this->getRuleText($fieldRule) . "',\n";
        }
        return substr($data, 0, -1);
    }

    public function createValidateLangFile(array $fieldsData, string $currentLang = null)
    {
        $currentLang = $currentLang ?? Lang::getLangSet();
        $targetPath = createPath($this->appPath, 'api', 'lang', 'validate', $currentLang, $this->modelName) . '.php';
        $sourcePath = $this->getStubPath('ValidateLang');
        // load field lang
        $fieldLangFile = createPath(base_path(), 'api', 'lang', 'field', $currentLang, $this->modelName) . '.php';
        if (file_exists($fieldLangFile)) {
            Lang::load($fieldLangFile);
        }
        $data = $this->buildValidateData($fieldsData);
        $replaceCondition = [
            '{{ data }}' => $data,
        ];
        try {
            $this->replaceAndWrite($sourcePath, $targetPath, function ($content) use ($replaceCondition) {
                return strtr($content, $replaceCondition);
            });
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function removeValidateLangFile(string $currentLang = null)
    {
        $currentLang = $currentLang ?? Lang::getLangSet();
        $targetPath = createPath($this->appPath, 'api', 'lang', 'validate', $currentLang, $this->modelName) . '.php';
        $this->fileSystem->remove($targetPath);
    }
}
