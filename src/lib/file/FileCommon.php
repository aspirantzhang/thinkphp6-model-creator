<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use think\Exception;
use think\facade\Lang;
use think\helper\Str;

class FileCommon
{
    protected $fileSystem;
    protected $stubPath;
    protected $appPath;
    protected $rootPath;
    protected $langPath;
    protected $tableName;
    protected $routeName;
    protected $modelName;
    protected $modelTitle;
    protected $instanceName;
    protected $modelType;

    public function __construct()
    {
        $this->fileSystem = new Filesystem();
        $this->appPath = base_path();
        $this->rootPath = root_path();
        $this->stubPath = createPath(dirname(__DIR__, 2), 'stubs');
        $this->langPath = createPath(dirname(__DIR__, 2), 'lang');
    }

    public function init(array $config)
    {
        $this->tableName = $config['name'];
        $this->routeName = $config['name'];
        $this->modelName = Str::studly($config['name']);
        $this->instanceName = Str::camel($config['name']);
        $this->modelTitle = $config['title'];
        $this->modelType = $config['type'] ?? 'main';
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

    public function readArrayFromFile(string $filePath): array
    {
        $result = include $filePath;
        return $result;
    }

    public function getStubPath(string $type, string $fileName = 'default')
    {
        $customPath = createPath($this->appPath, 'api', 'stubs', $type, $fileName) . '.stub';
        if ($this->fileSystem->exists($customPath)) {
            return $customPath;
        }
        return createPath($this->stubPath, $type, $fileName) . '.stub';
    }

    public function getDefaultLang(string $type, string $lang = null)
    {
        $lang = $lang ?? Lang::getLangSet();
        $customPath = createPath($this->appPath, 'api', 'lang', $type, $lang, 'default') . '.php';
        if ($this->fileSystem->exists($customPath)) {
            return $this->readArrayFromFile($customPath);
        }
        $defaultPath = createPath($this->langPath, $type, $lang, 'default') . '.php';
        if ($this->fileSystem->exists($defaultPath)) {
            return $this->readArrayFromFile($defaultPath);
        }
        return [];
    }
}
