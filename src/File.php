<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

use think\helper\Str;
use aspirantzhang\octopusModelCreator\lib\Validate;

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
        $fileTypes = $fileTypes ?: ['controller', 'model', 'view', 'logic', 'service', 'route', 'validate', 'langLayout', 'langField', 'validateModified'];
        try {
            foreach ($fileTypes as $type) {
                switch ($type) {
                    case 'langLayout':
                        $this->removeLangLayout();
                        break;
                    case 'langField':
                        $this->removeLangField();
                        break;
                    case 'validateModified':
                        $this->removeValidateFile();
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

    public function removeLangField(): void
    {
        $filePath = createPath($this->appPath, 'api', 'lang', 'field', $this->currentLang, $this->tableName) . '.php';

        if (is_file($filePath) && unlink($filePath) === false) {
            throw new \Exception(__('could not remove file', ['filePath' => $filePath]));
        }
    }

    public function createValidateFile($fieldsData)
    {
        $validateData = (new Validate($this->tableName, $fieldsData))->getData();

        $filePath = createPath($this->appPath, 'api', 'validate', $this->modelName) . '.php';
        $stubPath = createPath($this->appPath, 'api', 'validate', '_validate') . '.stub';

        $ruleText = '';
        foreach ($validateData['rules'] as $ruleKey => $ruleValue) {
            $ruleText .= "        '" . strtr($ruleKey, [$this->tableName . '@' => '']) . "' => '" . $ruleValue . "',\n";
        }
        $ruleText = substr($ruleText, 0, -1);

        $messageText = '';
        foreach ($validateData['messages'] as $msgKey => $msgValue) {
            if (strpos($msgKey, ':')) {
                $msgKey = substr($msgKey, 0, strpos($msgKey, ':'));
            }
            $messageText .= "        '" . $msgKey . "' => '" . $msgValue . "',\n";
        }
        $messageText = substr($messageText, 0, -1);

        $sceneSave = $validateData['scenes']['save'] ? '\'' . implode('\', \'', $validateData['scenes']['save']) . '\'' : '';
        $sceneUpdate = $validateData['scenes']['update'] ? '\'' . implode('\', \'', $validateData['scenes']['update']) . '\'' : '';
        $sceneHome = $validateData['scenes']['home'] ? '\'' . implode('\', \'', $validateData['scenes']['home']) . '\'' : '';

        $sceneHomeExclude = '';
        foreach ($validateData['scenes']['homeExclude'] as $exclude) {
            $sceneHomeExclude .= "\n" . '            ->remove(\'' . $exclude . '\', \'require\')';
        }

        $content = file_get_contents($stubPath);
        $content = str_replace([
            '{%modelName%}',
            '{%rule%}',
            '{%message%}',
            '{%sceneSave%}',
            '{%sceneUpdate%}',
            '{%sceneHome%}',
            '{%sceneHomeExclude%}',
        ], [
            $this->modelName,
            $ruleText,
            $messageText,
            $sceneSave,
            $sceneUpdate,
            $sceneHome,
            $sceneHomeExclude,
        ], $content);

        makeDir(dirname($filePath));
        if (file_put_contents($filePath, $content) === false) {
            throw new \Exception(__('could not write file', ['filePath' => $filePath]));
        }
    }

    public function removeValidateFile(): void
    {
        $filePath = createPath($this->appPath, 'api', 'validate', $this->modelName) . '.php';

        if (is_file($filePath) && unlink($filePath) === false) {
            throw new \Exception(__('could not remove file', ['filePath' => $filePath]));
        }
    }
}
