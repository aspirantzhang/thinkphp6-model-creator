<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

class FieldLangTest extends BaseCase
{
    protected $fieldLang;
    protected $snapPath;
    protected $prodStubPath;
    protected $testStubPath;

    protected function setUp(): void
    {
        $this->snapPath = createPath(dirname(__DIR__, 2), 'snapshots', 'lib', 'file', 'FieldLang');
        $this->testStubPath = createPath(dirname(__DIR__, 2), 'stubs', 'lib', 'file', 'FieldLang', '');
        $this->prodStubPath = createPath(dirname(__DIR__, 3), 'src', 'stubs', 'FieldLang');
        $this->fieldLang = new FieldLang();
        parent::setUp();
    }

    public function testCreateFieldLangFile()
    {
        $this->fieldLang->init($this->singleMainTableConfig)->createFieldLangFile($this->fieldsData);
        $filePath = createPath(base_path(), 'api', 'lang', 'field', 'en-us', 'UnitTest') . '.php';
        $snapshotPath = createPath($this->snapPath, 'UnitTest') . '.php.snap';
        $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
        // specific lang
        $this->fieldLang->init($this->singleMainTableConfig)->createFieldLangFile($this->fieldsData, 'de-de');
        $filePath = createPath(base_path(), 'api', 'lang', 'field', 'de-de', 'UnitTest') . '.php';
        $snapshotPath = createPath($this->snapPath, 'UnitTest') . '.php.snap';
        $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
    }

    /**
    * @depends testCreateFieldLangFile
    */
    public function testRemoveFieldLangFile()
    {
        $this->fieldLang->init($this->singleMainTableConfig)->removeFieldLangFile();
        $filePath = createPath(base_path(), 'api', 'lang', 'field', 'en-us', 'UnitTest') . '.php';
        $this->assertFalse($this->fileSystem->exists($filePath));
        // specific lang
        $this->fieldLang->init($this->singleMainTableConfig)->removeFieldLangFile('de-de');
        $filePath = createPath(base_path(), 'api', 'lang', 'field', 'de-de', 'UnitTest') . '.php';
        $this->assertFalse($this->fileSystem->exists($filePath));
    }
}
