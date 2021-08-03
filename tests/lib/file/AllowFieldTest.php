<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

class AllowFieldTest extends BaseCase
{
    protected $allowField;
    protected $snapPath;
    protected $prodStubPath;
    protected $testStubPath;

    protected function setUp(): void
    {
        $this->snapPath = createPath(dirname(__DIR__, 2), 'snapshots', 'lib', 'file', 'AllowField');
        $this->testStubPath = createPath(dirname(__DIR__, 2), 'stubs', 'lib', 'file', 'AllowField', '');
        $this->prodStubPath = createPath(dirname(__DIR__, 3), 'src', 'stubs', 'AllowField');
        $this->allowField = new AllowField();
        parent::setUp();
    }

    public function testCreateAllowFieldsFile()
    {
        $this->allowField->init('unit-test', 'Unit Test')->createAllowFieldsFile($this->fieldsData);
        $filePath = createPath(root_path(), 'config', 'api', 'allowFields', 'UnitTest') . '.php';
        $snapshotPath = createPath($this->snapPath, 'UnitTest') . '.php.snap';
        $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
    }
    /**
    * @depends testCreateAllowFieldsFile
    */
    public function testRemoveAllowFieldsFile()
    {
        $this->allowField->init('unit-test', 'Unit Test')->removeAllowFieldsFile();
        $filePath = createPath(root_path(), 'config', 'api', 'allowFields', 'UnitTest') . '.php';
        $this->assertFalse($this->fileSystem->exists($filePath));
    }
}
