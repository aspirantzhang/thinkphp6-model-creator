<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

use think\Exception;

class FileCommonTest extends BaseCase
{
    protected $fileCommon;
    protected $basePath;
    protected $runtimePath;

    protected function setUp(): void
    {
        $this->basePath = createPath(dirname(__DIR__, 2), 'stubs', 'lib', 'file', 'FileCommon', '');
        $this->runtimePath = createPath(dirname(__DIR__, 3), 'runtime', 'file', 'FileCommon', '');
        $this->fileCommon = new FileCommon();
        parent::setUp();
    }

    public function testReplaceAndWrite()
    {
        $sourcePath = $this->basePath . 'replaceAndWrite.stub';
        $snapshotPath = $this->basePath . 'replaceAndWrite.snap';
        $targetPath = $this->runtimePath . 'replaceAndWrite.php';
        $this->fileCommon->replaceAndWrite($sourcePath, $targetPath, function ($content) {
            return strtr($content, ['{%a%}' => 'foo','{%b%}' => 'bar']);
        });
        $this->assertTrue(matchSnapshot($targetPath, $snapshotPath));

        try {
            $this->fileCommon->replaceAndWrite($sourcePath . '.notExist', $targetPath, function () {
            });
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(), 'unable to get file content: filePath=' . createPath($sourcePath . '.notExist'));
            return;
        }
        $this->fail();
    }
}
