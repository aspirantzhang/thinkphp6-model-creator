<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use think\Exception;

class AllowFieldTest extends \aspirantzhang\octopusModelCreator\TestCase
{
    protected $allowField;
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
        $this->snapPath = createPath(dirname(__DIR__, 2), 'snapshots', 'lib', 'file', 'AllowField');
        $this->testStubPath = createPath(dirname(__DIR__, 2), 'stubs', 'lib', 'file', 'AllowField', '');
        $this->prodStubPath = createPath(dirname(__DIR__, 3), 'src', 'stubs', 'AllowField');
        $this->allowField = new AllowField();
        $this->fileSystem = new Filesystem();
        parent::setUp();
    }

    public function testCreateAllowFieldsFile()
    {
        $this->allowField->init('unit-test', 'Unit Test')->createAllowFieldsFile($this->fieldsData);
        $filePath = createPath(root_path(), 'config', 'api', 'allowFields', 'UnitTest') . '.php';
        $snapshotPath = createPath($this->snapPath, 'UnitTest') . '.php.snap';
        $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
    }
}
