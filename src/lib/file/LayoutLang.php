<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

use think\Exception;
use think\facade\Lang;

class LayoutLang extends FileCommon
{
    public function createLayoutLangFile(string $lang = null)
    {
        $lang = $lang ?? Lang::getLangSet();
        $targetPath = createPath($this->appPath, 'api', 'lang', 'layout', $lang, $this->modelName) . '.php';
        $sourcePath = createPath($this->stubPath, 'LayoutLang', 'default') . '.stub';
        $i18n = $this->readLangConfig('layout', $lang);
        $replaceCondition = [
            '{{ tableName }}' => $this->tableName,
            '{{ modelTitle }}' => $this->modelTitle,
            '{{ listText }}' => $i18n['layout.default.list'] ?? 'layout.default.list',
            '{{ addText }}' => $i18n['layout.default.add'] ?? 'layout.default.add',
            '{{ editText }}' => $i18n['layout.default.edit'] ?? 'layout.default.edit',
            '{{ i18nText }}' => $i18n['layout.default.i18n'] ?? 'layout.default.i18n',
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
