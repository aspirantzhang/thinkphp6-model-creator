<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

class ValidateLangTest extends BaseCase
{
    protected $validateLang;
    protected $snapPath;
    protected $prodStubPath;
    protected $testStubPath;

    protected function setUp(): void
    {
        $this->snapPath = createPath(dirname(__DIR__, 2), 'snapshots', 'lib', 'file', 'ValidateLang');
        $this->testStubPath = createPath(dirname(__DIR__, 2), 'stubs', 'lib', 'file', 'ValidateLang', '');
        $this->prodStubPath = createPath(dirname(__DIR__, 3), 'src', 'stubs', 'ValidateLang');
        $this->validateLang = new ValidateLang();
        parent::setUp();
    }

    public function testCreateValidateLangFile()
    {
        $this->validateLang->init('unit-test', 'Unit Test')->createValidateLangFile($this->fieldsData);
        $filePath = createPath(base_path(), 'api', 'lang', 'validate', 'en-us', 'UnitTest') . '.php';
        $snapshotPath = createPath($this->snapPath, 'UnitTest') . '.php.snap';
        $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
        // specific lang
        $this->validateLang->init('unit-test', 'Unit Test')->createValidateLangFile($this->fieldsData, 'de-de');
        $filePath = createPath(base_path(), 'api', 'lang', 'validate', 'de-de', 'UnitTest') . '.php';
        $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
    }

    /**
    * @depends testCreateValidateLangFile
    */
    public function testRemoveValidateLangFile()
    {
        $this->validateLang->init('unit-test', 'Unit Test')->removeValidateLangFile();
        $filePath = createPath(base_path(), 'api', 'lang', 'validate', 'en-us', 'UnitTest') . '.php';
        $this->assertFalse($this->fileSystem->exists($filePath));
        // specific lang
        $this->validateLang->init('unit-test', 'Unit Test')->removeValidateLangFile('de-de');
        $filePath = createPath(base_path(), 'api', 'lang', 'validate', 'de-de', 'UnitTest') . '.php';
        $this->assertFalse($this->fileSystem->exists($filePath));
    }
}
