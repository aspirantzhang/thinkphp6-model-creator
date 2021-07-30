<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

use think\Exception;
use think\facade\Lang;

class FieldLang extends FileCommon
{
    private function buildFieldText(array $fieldsData)
    {
        $data = '';
        foreach ($fieldsData as $field) {
            $data = $data . "        '" . $field['name'] . "' => '" . $field['title'] . "',\n";
        }
        return substr($data, 0, -1);
    }

    public function createFieldLangFile(array $fieldsData, string $currentLang = null)
    {
        $currentLang = $currentLang ?? Lang::getLangSet();
        $data = $this->buildFieldText($fieldsData);
        $replaceCondition = [
            '{%tableName%}' => $this->tableName,
            '{%data%}' => $data,
        ];

        $targetPath = createPath($this->appPath, 'api', 'lang', 'field', $currentLang, $this->modelName) . '.php';
        $sourcePath = createPath($this->stubPath, 'FieldLang', 'default') . '.stub';

        try {
            $this->replaceAndWrite($sourcePath, $targetPath, function ($content) use ($replaceCondition) {
                return strtr($content, $replaceCondition);
            });
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
