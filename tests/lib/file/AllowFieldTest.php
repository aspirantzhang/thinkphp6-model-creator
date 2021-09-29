<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

class FilterTest extends BaseCase
{
    protected $allowField;
    protected $snapPath;
    protected $prodStubPath;
    protected $testStubPath;

    protected function setUp(): void
    {
        $this->snapPath = createPath(dirname(__DIR__, 2), 'snapshots', 'lib', 'file', 'Filter');
        $this->testStubPath = createPath(dirname(__DIR__, 2), 'stubs', 'lib', 'file', 'Filter', '');
        $this->prodStubPath = createPath(dirname(__DIR__, 3), 'src', 'stubs', 'Filter');
        $this->allowField = new Filter();
        parent::setUp();
    }

    public function testCreateFilterFile()
    {
        $this->allowField->init('unit-test', 'Unit Test')->createFilterFile($this->fieldsData);
        $filePath = createPath(root_path(), 'config', 'api', 'allowFields', 'UnitTest') . '.php';
        $snapshotPath = createPath($this->snapPath, 'UnitTest') . '.php.snap';
        $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
    }
    /**
    * @depends testCreateFilterFile
    */
    public function testRemoveFilterFile()
    {
        $this->allowField->init('unit-test', 'Unit Test')->removeFilterFile();
        $filePath = createPath(root_path(), 'config', 'api', 'allowFields', 'UnitTest') . '.php';
        $this->assertFalse($this->fileSystem->exists($filePath));
    }
}
