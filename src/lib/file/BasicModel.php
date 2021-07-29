<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

use Symfony\Component\Filesystem\Filesystem;
use think\Exception;
use think\helper\Str;

class BasicModel extends FileCommon
{
    protected $fileSystem;
    protected $fileTypes = ['controller', 'model', 'view', 'logic', 'service', 'route', 'validate'];
    protected $tableName;
    protected $routeName;
    protected $modelName;
    protected $instanceName;
    protected $appPath;
    protected $stubPath;

    public function __construct()
    {
        $this->fileSystem = new Filesystem();
        $this->appPath = base_path();
        $this->stubPath = createPath(dirname(__DIR__, 2), 'stubs');
    }

    public function init($tableName, $modelTitle)
    {
        $this->tableName = $tableName;
        $this->routeName = $tableName;
        $this->modelName = Str::studly($tableName);
        $this->instanceName = Str::camel($tableName);
        $this->modelTitle = $modelTitle;
        return $this;
    }

    public function createBasicModelFile(array $fileTypes = null)
    {
        $fileTypes = $fileTypes ?? $this->fileTypes;
        $replaceCondition = [
            '{%tableName%}' => $this->tableName,
            '{%routeName%}' => $this->routeName,
            '{%modelName%}' => $this->modelName,
            '{%instanceName%}' => $this->instanceName,
        ];
        try {
            foreach ($fileTypes as $type) {
                $sourcePath = createPath($this->stubPath, 'BasicModel', $type) . '.stub';
                $targetPath = createPath($this->appPath, 'api', $type, $this->modelName) . '.php';
                $this->replaceAndWrite($sourcePath, $targetPath, function ($content) use ($replaceCondition) {
                    return strtr($content, $replaceCondition);
                });
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function removeBasicModelFile(array $fileTypes = null)
    {
        $fileTypes = $fileTypes ?? $this->fileTypes;
        $filePaths = array_map(function ($type) {
            return createPath($this->appPath, 'api', $type, $this->modelName) . '.php';
        }, $fileTypes);
        $this->fileSystem->remove($filePaths);
    }
}
