<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

class LayoutLangTest extends BaseCase
{
    protected $layoutLang;
    protected $snapPath;
    protected $prodStubPath;
    protected $testStubPath;

    protected function setUp(): void
    {
        $this->snapPath = createPath(dirname(__DIR__, 2), 'snapshots', 'lib', 'file', 'LayoutLang');
        $this->testStubPath = createPath(dirname(__DIR__, 2), 'stubs', 'lib', 'file', 'LayoutLang', '');
        $this->prodStubPath = createPath(dirname(__DIR__, 3), 'src', 'stubs', 'LayoutLang');
        $this->layoutLang = new LayoutLang();
        parent::setUp();
    }

    public function testLayoutLangFile()
    {
        $this->layoutLang->init('unit-test', 'Unit Test')->createLayoutLangFile();
        $filePath = createPath(base_path(), 'api', 'lang', 'layout', 'en-us', 'UnitTest') . '.php';
        $snapshotPath = createPath($this->snapPath, 'UnitTest') . '.php.snap';
        $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
        // specific lang
        $this->layoutLang->init('unit-test', 'Unit Test')->createLayoutLangFile('de-de');
        $filePath = createPath(base_path(), 'api', 'lang', 'layout', 'de-de', 'UnitTest') . '.php';
        $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
    }

    /**
    * @depends testLayoutLangFile
    */
    public function testRemoveLayoutLangFile()
    {
        $this->layoutLang->init('unit-test', 'Unit Test')->removeLayoutLangFile();
        $filePath = createPath(base_path(), 'api', 'lang', 'layout', 'en-us', 'UnitTest') . '.php';
        $this->assertFalse($this->fileSystem->exists($filePath));
        // specific lang
        $this->layoutLang->init('unit-test', 'Unit Test')->removeLayoutLangFile('de-de');
        $filePath = createPath(base_path(), 'api', 'lang', 'layout', 'de-de', 'UnitTest') . '.php';
        $this->assertFalse($this->fileSystem->exists($filePath));
    }
}
