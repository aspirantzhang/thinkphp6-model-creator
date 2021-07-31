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

    public function testCreateValidateLang()
    {
        $this->validateLang->init('unit-test', 'Unit Test')->createValidateLang($this->fieldsData);
        $filePath = createPath(base_path(), 'api', 'lang', 'validate', 'en-us', 'UnitTest') . '.php';
        $snapshotPath = createPath($this->snapPath, 'UnitTest') . '.php.snap';
        $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
        // specific lang
        $this->validateLang->init('unit-test', 'Unit Test')->createValidateLang($this->fieldsData, 'de-de');
        $filePath = createPath(base_path(), 'api', 'lang', 'validate', 'de-de', 'UnitTest') . '.php';
        $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
    }
}
