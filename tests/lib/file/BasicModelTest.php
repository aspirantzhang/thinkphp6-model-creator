<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

use think\Exception;

class BasicModelTest extends BaseCase
{
    protected $basicModel;
    protected $snapPath;
    protected $prodStubPath;
    protected $testStubPath;
    protected $fileTypes;

    protected function setUp(): void
    {
        $this->snapPath = createPath(dirname(__DIR__, 2), 'snapshots', 'lib', 'file', 'BasicModel');
        $this->testStubPath = createPath(dirname(__DIR__, 2), 'stubs', 'lib', 'file', 'BasicModel', '');
        $this->prodStubPath = createPath(dirname(__DIR__, 3), 'src', 'stubs', 'BasicModel');
        $this->fileTypes = ['controller', 'model', 'view', 'logic', 'service', 'route', 'validate'];
        $this->basicModel = new BasicModel();
        parent::setUp();
    }

    public function testCreateBasicModelFile()
    {
        $this->basicModel->init($this->mainConfig)->createBasicModelFile();

        foreach ($this->fileTypes as $type) {
            $filePath = createPath(base_path(), 'api', $type, 'UnitTest') . '.php';
            $snapshotPath = createPath($this->snapPath, 'api', $type, 'UnitTest') . '.php.snap';
            $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
        }

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('unable to get file content: filePath=' . createPath($this->prodStubPath, 'notExist.stub'));
        $this->basicModel->init($this->mainConfig)->createBasicModelFile(['notExist']);
    }

    public function testRemoveBasicModelFile()
    {
        $this->basicModel->init($this->mainConfig)->removeBasicModelFile();
        $filePaths = array_map(function ($type) {
            return createPath(base_path(), 'api', $type, 'UnitTest') . '.php';
        }, $this->fileTypes);
        foreach ($filePaths as $filePath) {
            $this->assertFalse($this->fileSystem->exists($filePath));
        }
    }

    public function testCategoryModelBasicModel()
    {
        $this->basicModel->init($this->categoryConfig)->createBasicModelFile(['controller', 'model']);
        // controller
        $controllerFilePath = createPath(base_path(), 'api', 'controller', 'CategoryTable') . '.php';
        $controllerSnapshotPath = createPath($this->snapPath, 'api', 'controller', 'CategoryTable') . '.php.snap';
        $this->assertTrue(matchSnapshot($controllerFilePath, $controllerSnapshotPath));
        // model
        $modelFilePath = createPath(base_path(), 'api', 'model', 'CategoryTable') . '.php';
        $modelSnapshotPath = createPath($this->snapPath, 'api', 'model', 'CategoryTable') . '.php.snap';
        $this->assertTrue(matchSnapshot($modelFilePath, $modelSnapshotPath));
    }
}
