<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

use think\Exception;

class FileCommonTest extends \aspirantzhang\octopusModelCreator\TestCase
{
    protected $fileCommon;
    protected $basePath;
    protected $runtimePath;
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
        $this->basePath = createPath(dirname(__DIR__, 2), 'stubs', 'lib', 'file', 'FileCommon', '');
        $this->runtimePath = createPath(dirname(__DIR__, 3), 'runtime', 'file', 'FileCommon', '');
        $this->fileCommon = new FileCommon();
        parent::setUp();
    }

    public function testReplaceAndWrite()
    {
        $sourcePath = $this->basePath . 'replaceAndWrite.stub';
        $snapshotPath = $this->basePath . 'replaceAndWrite.snap';
        $targetPath = $this->runtimePath . 'replaceAndWrite.php';
        $this->fileCommon->replaceAndWrite($sourcePath, $targetPath, function ($content) {
            return strtr($content, ['{%a%}' => 'foo','{%b%}' => 'bar']);
        });
        $this->assertTrue(matchSnapshot($targetPath, $snapshotPath));

        try {
            $this->fileCommon->replaceAndWrite($sourcePath . '.notExist', $targetPath, function () {
            });
        } catch (Exception $e) {
            $this->assertEquals($e->getMessage(), 'unable to get file content: filePath=' . createPath($sourcePath . '.notExist'));
            return;
        }
        $this->fail();
    }
}
