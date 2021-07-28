<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use think\Exception;

class BasicModel
{
    protected $fileSystem;

    public function __construct()
    {
        $this->fileSystem = new Filesystem();
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
