<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

use think\helper\Str;

class File
{
    protected $appPath;
    protected $tableName;
    protected $routeName;
    protected $modelName;
    protected $modelTitle;
    protected $instanceName;
    protected $currentLang;

    public function init(string $tableName, string $modelTitle, string $currentLang)
    {
        $this->tableName = $tableName;
        $this->routeName = $tableName;
        $this->modelName = Str::studly($tableName);
        $this->instanceName = Str::camel($tableName);
        $this->modelTitle = $modelTitle;
        $this->currentLang = $currentLang;
        $this->appPath = base_path();
        return $this;
    }

    public function create(array $fileTypes = null)
    {
        $fileTypes = $fileTypes ?: ['controller', 'model', 'view', 'logic', 'service', 'route', 'validate', 'langLayout'];
        try {
            foreach ($fileTypes as $type) {
                switch ($type) {
                    case 'langLayout':
                        $this->createLangLayout();
                        break;
                    default:
                        $this->createBasicFile($type);
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function createBasicFile(string $type): void
    {
        $filePath = createPath($this->appPath, 'api', $type, $this->modelName) . '.php';

        if (!is_file($filePath)) {
            // read from template
            $content = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . $type . '.stub');
            // replace keyword
            $content = str_replace(['{%modelName%}', '{%instanceName%}', '{%tableName%}', '{%routeName%}'], [$this->modelName, $this->instanceName, $this->tableName, $this->routeName], $content);
            // check parent dir exists
            makeDir(dirname($filePath));
            // write content
            if (file_put_contents($filePath, $content) === false) {
                throw new \Exception(__('could not write file', ['filePath' => $filePath]));
            }
        } else {
            throw new \Exception(__('file already exists', ['filePath' => $filePath]));
        }
    }

    public function remove(array $fileTypes = null)
    {
        $fileTypes = $fileTypes ?: ['controller', 'model', 'view', 'logic', 'service', 'route', 'validate', 'langLayout'];
        try {
            foreach ($fileTypes as $type) {
                switch ($type) {
                    case 'langLayout':
                        $this->removeLangLayout();
                        break;
                    default:
                        $this->removeBasicFile($type);
                }
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function removeBasicFile(string $type): void
    {
        $filePath = createPath($this->appPath, 'api', $type, $this->modelName) . '.php';

        if (is_file($filePath) && unlink($filePath) === false) {
            throw new \Exception(__('could not remove file', ['filePath' => $filePath]));
        }
    }

    public function createLangLayout()
    {
        $filePath = createPath($this->appPath, 'api', 'lang', 'layout', $this->currentLang, $this->tableName) . '.php';

        if (!is_file($filePath)) {
            $listText = __('list');
            $addText = __('add');
            $editText = __('edit');
            $i18nText = __('i18n');
    
            $content = file_get_contents(createPath(__DIR__, 'stubs', 'lang-layout') . '.stub');
            $content = str_replace(
                ['{%tableName%}', '{%modelTitle%}', '{%listText%}', '{%addText%}', '{%editText%}', '{%i18nText%}'],
                [$this->tableName, $this->modelTitle, $listText, $addText, $editText, $i18nText],
                $content
            );
    
            // check parent dir exists
            makeDir(dirname($filePath));
            // write content
            if (file_put_contents($filePath, $content) === false) {
                throw new \Exception(__('could not write file', ['filePath' => $filePath]));
            }
        } else {
            throw new \Exception(__('file already exists', ['filePath' => $filePath]));
        }
    }

    public function removeLangLayout(): void
    {
        $filePath = createPath($this->appPath, 'api', 'lang', 'layout', $this->currentLang, $this->tableName) . '.php';

        if (is_file($filePath) && unlink($filePath) === false) {
            throw new \Exception(__('could not remove file', ['filePath' => $filePath]));
        }
    }

    public function createLangField(array $fieldsData)
    {
        $data = '';
        foreach ($fieldsData as $field) {
            $data = $data . "        '" . $field['name'] . "' => '" . $field['title'] . "',\n";
        }
        $data = substr($data, 0, -1);
        $content = <<<END
<?php

return [
    '$this->tableName' => [
$data
    ]
];

END;

        $filePath = createPath($this->appPath, 'api', 'lang', 'field', $this->currentLang, $this->tableName) . '.php';

        // check parent dir exists
        makeDir(dirname($filePath));
        // write content
        if (file_put_contents($filePath, $content) === false) {
            throw new \Exception(__('could not write file', ['filePath' => $filePath]));
        }
    }
}
