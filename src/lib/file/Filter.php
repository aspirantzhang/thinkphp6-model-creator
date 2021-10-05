<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

use think\Exception;

class Filter extends FileCommon
{
    private function buildFilterText(array $fieldsData)
    {
        $titleField = [];
        $uniqueValue = [];
        $ignoreFilter = [];
        $allowHome = [];
        $allowRead = [];
        $allowSave = [];
        $allowUpdate = [];
        $allowTranslate = [];

        foreach ($fieldsData as $field) {
            // title field
            if ($field['titleField'] ?? false) {
                array_push($titleField, $field['name']);
            }
            // unique value
            if ($field['uniqueValue'] ?? false) {
                array_push($uniqueValue, $field['name']);
            }
            // ignore filter
            if ($field['ignoreFilter'] ?? false) {
                array_push($ignoreFilter, $field['name']);
            }
            // home
            if ($field['allowHome'] ?? false) {
                array_push($allowHome, $field['name']);
            }
            // read
            if ($field['allowRead'] ?? false) {
                array_push($allowRead, $field['name']);
            }
            // save
            if ($field['allowSave'] ?? false) {
                array_push($allowSave, $field['name']);
            }
            // update
            if ($field['allowUpdate'] ?? false) {
                array_push($allowUpdate, $field['name']);
            }
            // translate
            if ($field['allowTranslate'] ?? false) {
                array_push($allowTranslate, $field['name']);
            }
        }

        $titleFieldText = $titleField ? $titleField[0] ?? '' : '';
        $uniqueValueText = $uniqueValue ? '\'' . implode('\', \'', $uniqueValue) . '\'' : '';
        $ignoreFilterText = $ignoreFilter ? '\'' . implode('\', \'', $ignoreFilter) . '\'' : '';
        $allowHomeText = $allowHome ? '\'' . implode('\', \'', $allowHome) . '\'' : '';
        $allowReadText = $allowRead ? '\'' . implode('\', \'', $allowRead) . '\'' : '';
        $allowSaveText = $allowSave ? '\'' . implode('\', \'', $allowSave) . '\'' : '';
        $allowUpdateText = $allowUpdate ? '\'' . implode('\', \'', $allowUpdate) . '\'' : '';
        $allowTranslateText = $allowTranslate ? '\'' . implode('\', \'', $allowTranslate) . '\'' : '';

        return [$titleFieldText, $uniqueValueText, $ignoreFilterText, $allowHomeText, $allowReadText, $allowSaveText, $allowUpdateText, $allowTranslateText];
    }

    public function createFilterFile(array $fieldsData)
    {
        list($titleFieldText, $uniqueValueText, $ignoreFilterText, $allowHomeText, $allowReadText, $allowSaveText, $allowUpdateText, $allowTranslateText) = $this->buildFilterText($fieldsData);
        $replaceCondition = [
            '{{ titleFieldText }}' => $titleFieldText,
            '{{ uniqueValueText }}' => $uniqueValueText,
            '{{ ignoreFilterText }}' => $ignoreFilterText,
            '{{ allowHomeText }}' => $allowHomeText,
            '{{ allowReadText }}' => $allowReadText,
            '{{ allowSaveText }}' => $allowSaveText,
            '{{ allowUpdateText }}' => $allowUpdateText,
            '{{ allowTranslateText }}' => $allowTranslateText,
        ];
        $targetPath = createPath($this->rootPath, 'config', 'api', 'model', $this->modelName) . '.php';
        $sourcePath = $this->getStubPath('Filter');
        try {
            $this->replaceAndWrite($sourcePath, $targetPath, function ($content) use ($replaceCondition) {
                return strtr($content, $replaceCondition);
            });
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function removeFilterFile()
    {
        $targetPath = createPath($this->rootPath, 'config', 'api', 'model', $this->modelName) . '.php';
        $this->fileSystem->remove($targetPath);
    }
}
