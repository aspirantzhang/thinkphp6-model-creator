<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

use aspirantzhang\octopusModelCreator\ModelCreator;

class ValidateTest extends BaseCase
{
    protected $validate;
    protected $snapPath;
    protected $prodStubPath;
    protected $testStubPath;

    protected function setUp(): void
    {
        $this->snapPath = createPath(dirname(__DIR__, 2), 'snapshots', 'lib', 'file', 'Validate');
        $this->testStubPath = createPath(dirname(__DIR__, 2), 'stubs', 'lib', 'file', 'Validate', '');
        $this->prodStubPath = createPath(dirname(__DIR__, 3), 'src', 'stubs', 'Validate');
        $this->validate = new Validate();
        parent::setUp();
    }

    public function testCreateValidateFile()
    {
        $demoFieldsData = ModelCreator::helper()->extractAllFields($this->getDemo('default-field')['data']);
        $this->validate->init($this->singleMainTableConfig)->createValidateFile($demoFieldsData);
        $filePath = createPath(base_path(), 'api', 'validate', 'UnitTest') . '.php';
        $snapshotPath = createPath($this->snapPath, 'UnitTest') . '.php.snap';
        $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
    }

    /**
    * @depends testCreateValidateFile
    */
    public function testRemoveValidateFile()
    {
        $this->validate->init($this->singleMainTableConfig)->removeValidateFile();
        $filePath = createPath(base_path(), 'api', 'validate', 'UnitTest') . '.php';
        $this->assertFalse($this->fileSystem->exists($filePath));
    }
}
