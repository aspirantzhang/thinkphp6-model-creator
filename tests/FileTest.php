<?php

declare(strict_types=1);

namespace aspirantzhang\thinkphp6ModelCreator;

function base_path(): string
{
    return 'runtime' . DIRECTORY_SEPARATOR . 'file';
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
        ModelCreator::file('unit-test', 'Unit Test', 'en-us')->create();
        // basic types
        $basicTypes = ['controller', 'model', 'view', 'logic', 'service', 'route', 'validate'];
        foreach ($basicTypes as $types) {
            $filePath = createPath(base_path(), 'api', $types, 'UnitTest') . '.php';
            $snapshotPath = createPath(__DIR__, '__snapshots__', $types, 'UnitTest') . '.php.snap';
            $this->assertTrue(is_dir(createPath(base_path(), 'api', $types)));
            $this->assertTrue(is_file($filePath));
            $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
        }
        // lang layout
        $langLayoutPath = createPath(base_path(), 'api', 'lang', 'layout', 'en-us', 'unit-test') . '.php';
        $langLayoutSnapshotPath = createPath(__DIR__, '__snapshots__', 'lang', 'layout', 'en-us', 'unit-test') . '.php.snap';
        $this->assertTrue(is_file($langLayoutPath));
        $this->assertTrue(matchSnapshot($langLayoutPath, $langLayoutSnapshotPath));
    }

    public function testFileRemoveSuccessfully()
    {
        ModelCreator::file('unit-test', 'Unit Test', 'en-us')->remove();
        // basic types
        $basicTypes = ['controller', 'model', 'view', 'logic', 'service', 'route', 'validate'];
        foreach ($basicTypes as $types) {
            $filePath = createPath(base_path(), 'api', $types, 'UnitTest') . '.php';
            $this->assertFalse(is_file($filePath));
        }
        // lang layout
        $langLayoutPath = createPath(base_path(), 'api', 'lang', 'layout', 'en-us', 'unit-test') . '.php';
        $this->assertFalse(is_file($langLayoutPath));
    }

    
    public function testCreateBasicFileSuccessfully()
    {
        deleteDirectory(base_path());
        ModelCreator::file('unit-test', 'Unit Test', 'en-us')->createBasicFile('controller');
        $filePath = createPath(base_path(), 'api', 'controller', 'UnitTest') . '.php';
        $snapshotPath = createPath(__DIR__, '__snapshots__', 'controller', 'UnitTest') . '.php.snap';
        $this->assertTrue(is_dir(createPath(base_path(), 'api', 'controller')));
        $this->assertTrue(is_file($filePath));
        $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
    }

    public function testCreateBasicFileFailed()
    {
        deleteDirectory(base_path());
        ModelCreator::file('unit-test', 'Unit Test', 'en-us')->createBasicFile('controller');
        try {
            ModelCreator::file('unit-test', 'Unit Test', 'en-us')->createBasicFile('controller');
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), 'file already exists:' . createPath(base_path(), 'api', 'controller', 'UnitTest') . '.php');
            return;
        }
        $this->fail();
    }

    public function testLangLayoutSuccessfully()
    {
        deleteDirectory(base_path());
        ModelCreator::file('unit-test', 'Unit Test', 'en-us')->createLangLayout();
        $filePath = createPath(base_path(), 'api', 'lang', 'layout', 'en-us', 'unit-test') . '.php';
        $snapshotPath = createPath(__DIR__, '__snapshots__', 'lang', 'layout', 'en-us', 'unit-test') . '.php.snap';
        $this->assertTrue(is_file($filePath));
        $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
    }

    public function testLangLayoutFailed()
    {
        deleteDirectory(base_path());
        ModelCreator::file('unit-test', 'Unit Test', 'en-us')->createLangLayout();
        try {
            ModelCreator::file('unit-test', 'Unit Test', 'en-us')->createLangLayout();
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), 'file already exists:' . createPath(base_path(), 'api', 'lang', 'layout', 'en-us', 'unit-test') . '.php');
            return;
        }
        $this->fail();
    }
}
