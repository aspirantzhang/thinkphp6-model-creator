<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use think\Exception;
use think\helper\Str;

if (!function_exists('base_path')) {
    function base_path()
    {
        return createPath(dirname(__DIR__, 3), 'runtime');
    }
}
if (!function_exists('root_path')) {
    function root_path()
    {
        return createPath(dirname(__DIR__, 3), 'runtime');
    }
}
class FileCommon
{
    protected $fileSystem;
    protected $stubPath;
    protected $appPath;
    protected $rootPath;
    protected $tableName;
    protected $routeName;
    protected $modelName;
    protected $modelTitle;
    protected $instanceName;

    public function __construct()
    {
        $this->fileSystem = new Filesystem();
        $this->appPath = base_path();
        $this->rootPath = root_path();
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

    public function replaceAndWrite(string $sourcePath, string $targetPath, callable $callback)
    {
        try {
            $content = $this->getContent($sourcePath);
            if (is_callable($callback)) {
                $content = call_user_func($callback, $content);
            }
            $this->writeFile($targetPath, $content);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function writeFile(string $path, string $content)
    {
        try {
            $this->fileSystem->dumpFile($path, $content);
        } catch (IOExceptionInterface $e) {
            throw new Exception(__('unable to write file content', ['filePath' => $path]));
        }
    }

    public function getContent(string $path): string
    {
        if (
            $this->fileSystem->exists($path) &&
            false !== ($result = file_get_contents($path))
        ) {
            return $result;
        }
        throw new Exception(__('unable to get file content', ['filePath' => $path]));
    }
}
