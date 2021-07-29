<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

use think\Exception;

class BasicModelTest extends \aspirantzhang\octopusModelCreator\TestCase
{
    protected $basicModel;
    protected $snapPath;
    protected $prodStubPath;
    protected $testStubPath;

    protected function setUp(): void
    {
        $this->snapPath = createPath(dirname(__DIR__, 2), 'snapshots', 'lib', 'file', 'BasicModel');
        $this->testStubPath = createPath(dirname(__DIR__, 2), 'stubs', 'lib', 'file', 'BasicModel', '');
        $this->prodStubPath = createPath(dirname(__DIR__, 3), 'src', 'stubs', 'BasicModel');
        $this->basicModel = new BasicModel();
        parent::setUp();
    }

    public function testCreateBasicModelFile()
    {
        $this->basicModel->init('unit-test', 'Unit Test')->createBasicModelFile();

        $fileTypes = ['controller', 'model', 'view', 'logic', 'service', 'route', 'validate'];

        foreach ($fileTypes as $type) {
            $filePath = createPath(base_path(), 'api', $type, 'UnitTest') . '.php';
            $snapshotPath = createPath($this->snapPath, 'api', $type, 'UnitTest') . '.php.snap';
            $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
        }

        try {
            $this->basicModel->init('unit-test', 'Unit Test')->createBasicModelFile(['notExist']);
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(), 'unable to get file content: filePath=' . createPath($this->prodStubPath, 'notExist.stub'));
            return;
        }
        $this->fail();
    }
}
