<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

use think\Exception;
use think\facade\Lang;
use think\facade\Config;

class FieldLang extends FileCommon
{
    private function buildFieldText(array $fieldsData)
    {
        $data = '';
        $reservedFields = Config::get('reserved.reserved_field');
        foreach ($fieldsData as $field) {
            if (in_array($field['name'], $reservedFields)) {
                continue;
            }
            $data = $data . "        '" . $field['name'] . "' => '" . $field['title'] . "',\n";
        }
        return substr($data, 0, -1);
    }

    public function createFieldLangFile(array $fieldsData, string $currentLang = null)
    {
        $currentLang = $currentLang ?? Lang::getLangSet();
        $data = $this->buildFieldText($fieldsData);
        $replaceCondition = [
            '{{ tableName }}' => $this->tableName,
            '{{ data }}' => $data,
        ];

        $targetPath = createPath($this->appPath, 'api', 'lang', 'field', $currentLang, $this->modelName) . '.php';
        $sourcePath = $this->getStubPath('FieldLang');

        try {
            $this->replaceAndWrite($sourcePath, $targetPath, function ($content) use ($replaceCondition) {
                return strtr($content, $replaceCondition);
            });
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function removeFieldLangFile(string $currentLang = null)
    {
        $currentLang = $currentLang ?? Lang::getLangSet();
        $targetPath = createPath($this->appPath, 'api', 'lang', 'field', $currentLang, $this->modelName) . '.php';
        $this->fileSystem->remove($targetPath);
    }
}
