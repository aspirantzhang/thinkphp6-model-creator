<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use think\Exception;

class FieldLangTest extends \aspirantzhang\octopusModelCreator\TestCase
{
    protected $fieldLang;
    protected $snapPath;
    protected $prodStubPath;
    protected $testStubPath;
    protected $fileSystem;
    protected $fieldsData = [
        [
            "name" => "nickname",
            "title" => "Nick Name",
            "type" => "input",
            "settings" => [
                "validate" => [
                    "require",
                    "length"
                ],
                "options" => [
                    "length" => [
                        "min" => 4,
                        "max" => 32
                    ]
                ]
            ],
            "allowHome" => true,
            "allowRead" => true,
            "allowSave" => true,
            "allowUpdate" => true,
            "allowTranslate" => true
        ],
        [
            "name" => "gender",
            "title" => "Gender",
            "type" => "radio",
            "data" => [
                [
                    "title" => "Mx",
                    "value" => "mx"
                ],
                [
                    "title" => "Mr",
                    "value" => "mr"
                ],
                [
                    "title" => "Ms",
                    "value" => "ms"
                ]
            ],
            "settings" => [
                "validate" => [
                    "require"
                ]
            ],
            "allowHome" => true,
            "allowRead" => true,
            "allowSave" => true,
            "allowUpdate" => true
        ],
        [
            "name" => "married",
            "title" => "Married",
            "type" => "switch",
            "hideInColumn" => true,
            "data" => [
                [
                    "title" => "Yes",
                    "value" => 1
                ],
                [
                    "title" => "No",
                    "value" => 0
                ]
            ],
            "settings" => [
                "display" => [
                    "listSorter"
                ],
                "validate" => [
                    "require"
                ]
            ],
            "allowHome" => true,
            "allowRead" => true,
            "allowUpdate" => true,
            "allowSave" => true
        ]
    ];

    protected function setUp(): void
    {
        $this->snapPath = createPath(dirname(__DIR__, 2), 'snapshots', 'lib', 'file', 'FieldLang');
        $this->testStubPath = createPath(dirname(__DIR__, 2), 'stubs', 'lib', 'file', 'FieldLang', '');
        $this->prodStubPath = createPath(dirname(__DIR__, 3), 'src', 'stubs', 'FieldLang');
        $this->fieldLang = new FieldLang();
        $this->fileSystem = new Filesystem();
        parent::setUp();
    }

    public function testCreateFieldLangFile()
    {
        $this->fieldLang->init('unit-test', 'Unit Test')->createFieldLangFile($this->fieldsData);
        $filePath = createPath(base_path(), 'api', 'lang', 'field', 'en-us', 'UnitTest') . '.php';
        $snapshotPath = createPath($this->snapPath, 'UnitTest') . '.php.snap';
        $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
        // specific lang
        $this->fieldLang->init('unit-test', 'Unit Test')->createFieldLangFile($this->fieldsData, 'de-de');
        $filePath = createPath(base_path(), 'api', 'lang', 'field', 'de-de', 'UnitTest') . '.php';
        $snapshotPath = createPath($this->snapPath, 'UnitTest') . '.php.snap';
        $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
    }
}
