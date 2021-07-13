<?php

declare(strict_types=1);

namespace aspirantzhang\thinkphp6ModelCreator;

use think\helper\Str;

class File
{
    protected $appPath;
    protected $tableName;
    protected $routeName;
    protected $modelName;
    protected $instanceName;
    protected $error = '';

    public function make(string $tableName, array $fileTypes = null)
    {
        $this->tableName = $tableName;
        $this->routeName = $tableName;
        $this->modelName = Str::studly($tableName);
        $this->instanceName = Str::camel($tableName);
        $this->appPath = base_path();
        $fileTypes = $fileTypes ?: ['controller', 'model', 'view', 'logic', 'service', 'route', 'validate'];
        try {
            $this->createFile($fileTypes);
        } catch (\Throwable $e) {
            throw new \Exception($this->error);
        }
    }

    public function createFile(array $fileTypes): void
    {
        foreach ($fileTypes as $type) {
            $filePath = $this->createPath($this->appPath, 'api', $type, $this->modelName) . '.php';

            if (!is_file($filePath)) {
                // read from template
                $content = file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'stubs' . DIRECTORY_SEPARATOR . $type . '.stub');
                // replace keyword
                $content = str_replace(['{%modelName%}', '{%instanceName%}', '{%tableName%}', '{%routeName%}'], [$this->modelName, $this->instanceName, $this->tableName, $this->routeName], $content);
                // check parent dir exists
                $this->checkAndMakeDir(dirname($filePath));
                // write content
                if (file_put_contents($filePath, $content) === false) {
                    $this->error = __('could not write file', ['filePath' => $filePath]);
                    throw new \Exception();
                }
            } else {
                $this->error = __('file already exists', ['filePath' => $filePath]);
                throw new \Exception();
            }
        }
    }

    protected function checkAndMakeDir(string $dirname): void
    {
        if (!is_dir($dirname)) {
            mkdir($dirname, 0755, true);
        }
    }

    protected function createPath(string ...$path): string
    {
        return join(DIRECTORY_SEPARATOR, $path);
    }
}
