<?php

declare(strict_types=1);

namespace aspirantzhang\thinkphp6ModelCreator;

function base_path(): string
{
    return 'runtime';
}
function __(string $name, array $vars = [], string $lang = ''): string
{
    return $name . ':' . join('|', $vars);
}

function deleteDirectory($dir)
{
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }

    return rmdir($dir);
}

function createPath(string ...$path): string
{
        return join(DIRECTORY_SEPARATOR, $path);
}

function matchSnapshot(string $filePath, string $snapshotPath)
{
    $fileContent = file_get_contents($filePath);
    $snapshotContent = file_get_contents($snapshotPath);
    if ($fileContent === false || $snapshotContent === false || $fileContent !== $snapshotContent) {
        return false;
    }
    return true;
}

class FileTest extends TestCase
{
    public function testFileCreatedSuccessfully()
    {
        deleteDirectory(base_path());
        ModelCreator::file('unit-test')->create();
        $fileTypes = ['controller', 'model', 'view', 'logic', 'service', 'route', 'validate'];
        foreach ($fileTypes as $types) {
            $filePath = createPath(base_path(), 'api', $types, 'UnitTest') . '.php';
            $snapshotPath = createPath(__DIR__, '__snapshots__', $types, 'UnitTest') . '.php.snap';
            $this->assertTrue(is_dir(createPath(base_path(), 'api', $types)));
            $this->assertTrue(is_file($filePath));
            $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
        }
    }

    public function testFileCreatedFailed()
    {
        try {
            ModelCreator::file('unit-test')->create();
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), 'file already exists:' . createPath('runtime', 'api', 'controller', 'UnitTest') . '.php');
            return;
        }
        $this->fail();
    }
}
