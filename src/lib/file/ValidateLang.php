<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

use think\Exception;
use think\facade\Lang;

class ValidateLang extends FileCommon
{
    private function getValidateText($langKey)
    {
        $fieldName = substr($langKey, 0, strpos($langKey, '#'));
        $fieldName = strtr($fieldName, ['@' => '.']);
        $ruleName = substr($langKey, strpos($langKey, '#') + 1);
        $option = '';
        if (strpos($ruleName, ':')) {
            $ruleName = substr($ruleName, 0, strpos($ruleName, ':'));
            $option = substr($langKey, strpos($langKey, ':') + 1);
            if ($option) {
                $option = strtr($option, [',' => ' - ']);
            }
        }
        return __('validate.' . $ruleName, ['field' => __($fieldName), 'option' => $option]);
    }

    public function createValidateLangFile(array $fieldsData, string $currentLang = null)
    {
        $validateMessages = (new Validate())->init($this->tableName, $this->modelTitle)->getMessages($fieldsData);

        $exclude = ['id.require', 'id.number', 'ids.require', 'ids.numberArray', 'status.numberTag', 'page.number', 'per_page.number', 'create_time.require', 'create_time.dateTimeRange'];
        $msgs = array_diff_key($validateMessages, array_flip($exclude));
        $data = '';
        foreach ($msgs as $langKey) {
            $data .= '    \'' . $langKey . '\' => \'' . $this->getValidateText($langKey) . "',\n";
        }
        $data = substr($data, 0, -1);

        $currentLang = $currentLang ?? Lang::getLangSet();
        $targetPath = createPath($this->appPath, 'api', 'lang', 'validate', $currentLang, $this->modelName) . '.php';
        $sourcePath = createPath($this->stubPath, 'ValidateLang', 'default') . '.stub';
        $replaceCondition = [
            '{%data%}' => $data,
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
