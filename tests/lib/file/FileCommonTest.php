<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

use think\Exception;

class FileCommonTest extends BaseCase
{
    protected $fileCommon;
    protected $stubPath;
    protected $runtimePath;

    protected function setUp(): void
    {
        $this->stubPath = createPath(dirname(__DIR__, 2), 'stubs', 'lib', 'file', 'FileCommon', '');
        $this->runtimePath = createPath(dirname(__DIR__, 3), 'runtime', 'file', 'FileCommon', '');
        $this->fileCommon = new FileCommon();
        parent::setUp();
    }

    public function testReplaceAndWrite()
    {
        $sourcePath = $this->stubPath . 'replaceAndWrite.stub';
        $snapshotPath = $this->stubPath . 'replaceAndWrite.snap';
        $targetPath = $this->runtimePath . 'replaceAndWrite.php';
        $this->fileCommon->replaceAndWrite($sourcePath, $targetPath, function ($content) {
            return strtr($content, ['{{ a }}' => 'foo','{{ b }}' => 'bar']);
        });
        $this->assertTrue(matchSnapshot($targetPath, $snapshotPath));

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('unable to get file content: filePath=' . createPath($sourcePath . '.notExist'));
        $this->fileCommon->replaceAndWrite($sourcePath . '.notExist', $targetPath, function () {
        });
    }

    public function testReadArrayFromFile()
    {
        $result = $this->fileCommon->readArrayFromFile($this->stubPath . 'readArrayFromFile.php');
        $this->assertEqualsCanonicalizing($result, ['foo' => 'bar']);
    }

    public function testReadLangConfig()
    {
        // default config
        $result = $this->fileCommon->getDefaultLang('layout');
        $this->assertEquals($result['default.list'], ' List');
        // custom config
        $stubPath = $this->stubPath . 'readLangConfig.php.stub';
        $customPath = createPath(base_path(), 'api', 'lang', 'layout', 'en-us', 'default') . '.php';
        $this->fileSystem->copy($stubPath, $customPath);
        $result = $this->fileCommon->getDefaultLang('layout');
        $this->assertEquals($result['default.list'], 'custom');
    }

    public function testGetWithRelationReturnString()
    {
        $actual = $this->fileCommon->init([
            'name' => 'unit-test',
            'title' => 'Unit Test',
            'type' => 'category',
            'withRelation' => ['model1', 'model2'],
        ])->getWithRelation('string');
        $this->assertEquals('\'model1\', \'model2\'', $actual);

        $actual2 = $this->fileCommon->init([
            'name' => 'unit-test',
            'title' => 'Unit Test',
            'type' => 'category',
            'withRelation' => ['model1'],
        ])->getWithRelation('string');
        $this->assertEquals('\'model1\'', $actual2);
    }
}
