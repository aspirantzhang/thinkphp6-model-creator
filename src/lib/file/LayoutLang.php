<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

use think\Exception;
use think\facade\Lang;

class LayoutLang extends FileCommon
{
    public function createLayoutLangFile(string $currentLang = null)
    {
        $currentLang = $currentLang ?? Lang::getLangSet();
        $targetPath = createPath($this->appPath, 'api', 'lang', 'layout', $currentLang, $this->modelName) . '.php';
        $sourcePath = createPath($this->stubPath, 'LayoutLang', 'default') . '.stub';
        $replaceCondition = [
            '{{ tableName }}' => $this->tableName,
            '{{ modelTitle }}' => $this->modelTitle,
            '{{ listText }}' => __('layout.default.list'),
            '{{ addText }}' => __('layout.default.add'),
            '{{ editText }}' => __('layout.default.edit'),
            '{{ i18nText }}' => __('layout.default.i18n'),
        ];
        try {
            $this->replaceAndWrite($sourcePath, $targetPath, function ($content) use ($replaceCondition) {
                return strtr($content, $replaceCondition);
            });
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function removeLayoutLangFile(string $currentLang = null)
    {
        $currentLang = $currentLang ?? Lang::getLangSet();
        $targetPath = createPath($this->appPath, 'api', 'lang', 'layout', $currentLang, $this->modelName) . '.php';
        $this->fileSystem->remove($targetPath);
    }
}
