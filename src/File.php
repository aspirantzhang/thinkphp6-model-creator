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

    public function init(string $tableName)
    {
        $this->tableName = $tableName;
        $this->routeName = $tableName;
        $this->modelName = Str::studly($tableName);
        $this->instanceName = Str::camel($tableName);
        $this->appPath = base_path();
        return $this;
    }

    public function create(array $fileTypes = null)
    {
        $fileTypes = $fileTypes ?: ['controller', 'model', 'view', 'logic', 'service', 'route', 'validate'];
        try {
            $this->createFile($fileTypes);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    protected function createFile(array $fileTypes): void
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
                    throw new \Exception(__('could not write file', ['filePath' => $filePath]));
                }
            } else {
                throw new \Exception(__('file already exists', ['filePath' => $filePath]));
            }
        }
    }

    public function remove(array $fileTypes = null)
    {
        $fileTypes = $fileTypes ?: ['controller', 'model', 'view', 'logic', 'service', 'route', 'validate'];
        try {
            $this->removeFile($fileTypes);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    protected function removeFile(array $fileTypes): void
    {
        foreach ($fileTypes as $type) {
            $filePath = $this->createPath($this->appPath, 'api', $type, $this->modelName) . '.php';

            if (is_file($filePath) && unlink($filePath) === false) {
                throw new \Exception(__('could not remove file', ['filePath' => $filePath]));
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
