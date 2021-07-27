<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

use think\facade\Lang;

function base_path(): string
{
    return 'runtime' . DIRECTORY_SEPARATOR . 'file';
}

function root_path(): string
{
    return 'runtime' . DIRECTORY_SEPARATOR . 'file';
}

function __(string $name, array $vars = [], string $lang = ''): string
{
    return $name . ':' . join('|', $vars);
}

class FileTest extends TestCase
{
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

    public function testFileCreatedSuccessfully()
    {
        deleteDir(base_path());
        ModelCreator::file('unit-test', 'Unit Test', 'en-us')->create();
        // basic types
        $basicTypes = ['controller', 'model', 'view', 'logic', 'service', 'route', 'validate'];
        foreach ($basicTypes as $types) {
            $filePath = createPath(base_path(), 'api', $types, 'UnitTest') . '.php';
            $snapshotPath = createPath(__DIR__, '__snapshots__', $types, 'UnitTest') . '.php.snap';
            $this->assertTrue(is_dir(createPath(base_path(), 'api', $types)));
            $this->assertTrue(is_file($filePath));
            $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
        }
        // lang layout
        $langLayoutPath = createPath(base_path(), 'api', 'lang', 'layout', 'en-us', 'UnitTest') . '.php';
        $langLayoutSnapshotPath = createPath(__DIR__, '__snapshots__', 'lang', 'layout', 'en-us', 'UnitTest') . '.php.snap';
        $this->assertTrue(is_file($langLayoutPath));
        $this->assertTrue(matchSnapshot($langLayoutPath, $langLayoutSnapshotPath));
    }

    public function testFileRemoveSuccessfully()
    {
        ModelCreator::file('unit-test', 'Unit Test', 'en-us')->remove();
        // basic types
        $basicTypes = ['controller', 'model', 'view', 'logic', 'service', 'route', 'validate'];
        foreach ($basicTypes as $types) {
            $filePath = createPath(base_path(), 'api', $types, 'UnitTest') . '.php';
            $this->assertFalse(is_file($filePath));
        }
        // lang layout
        $langLayoutPath = createPath(base_path(), 'api', 'lang', 'layout', 'en-us', 'UnitTest') . '.php';
        $this->assertFalse(is_file($langLayoutPath));
        // lang field
        $langFieldPath = createPath(base_path(), 'api', 'lang', 'field', 'en-us', 'UnitTest') . '.php';
        $this->assertFalse(is_file($langFieldPath));
        // validate modified
        $langValidatePath = createPath(base_path(), 'api', 'validate', 'UnitTest') . '.php';
        $this->assertFalse(is_file($langValidatePath));
        // lang validate i18n
        $langValidateI18nPath = createPath(base_path(), 'api', 'lang', 'validate', 'en-us', 'UnitTest') . '.php';
        $this->assertFalse(is_file($langValidateI18nPath));
    }

    
    public function testCreateBasicFileSuccessfully()
    {
        deleteDir(base_path());
        ModelCreator::file('unit-test', 'Unit Test', 'en-us')->createBasicFile('controller');
        $filePath = createPath(base_path(), 'api', 'controller', 'UnitTest') . '.php';
        $snapshotPath = createPath(__DIR__, '__snapshots__', 'controller', 'UnitTest') . '.php.snap';
        $this->assertTrue(is_dir(createPath(base_path(), 'api', 'controller')));
        $this->assertTrue(is_file($filePath));
        $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
    }

    public function testCreateBasicFileFailed()
    {
        deleteDir(base_path());
        ModelCreator::file('unit-test', 'Unit Test', 'en-us')->createBasicFile('controller');
        try {
            ModelCreator::file('unit-test', 'Unit Test', 'en-us')->createBasicFile('controller');
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), 'file already exists:' . createPath(base_path(), 'api', 'controller', 'UnitTest') . '.php');
            return;
        }
        $this->fail();
    }

    public function testLangLayoutSuccessfully()
    {
        deleteDir(base_path());
        ModelCreator::file('unit-test', 'Unit Test', 'en-us')->createLangLayout();
        $filePath = createPath(base_path(), 'api', 'lang', 'layout', 'en-us', 'UnitTest') . '.php';
        $snapshotPath = createPath(__DIR__, '__snapshots__', 'lang', 'layout', 'en-us', 'UnitTest') . '.php.snap';
        $this->assertTrue(is_file($filePath));
        $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
    }

    public function testLangLayoutFailed()
    {
        deleteDir(base_path());
        ModelCreator::file('unit-test', 'Unit Test', 'en-us')->createLangLayout();
        try {
            ModelCreator::file('unit-test', 'Unit Test', 'en-us')->createLangLayout();
        } catch (\Exception $e) {
            $this->assertEquals($e->getMessage(), 'file already exists:' . createPath(base_path(), 'api', 'lang', 'layout', 'en-us', 'UnitTest') . '.php');
            return;
        }
        $this->fail();
    }

    public function testLangFieldSuccessfully()
    {
        $fields = [
            [
                'name' => 'foo',
                'title' => 'bar',
            ],
            [
                'name' => 'foo2',
                'title' => 'bar2',
            ],
        ];
        ModelCreator::file('unit-test', 'Unit Test', 'en-us')->createLangField($fields);
        $filePath = createPath(base_path(), 'api', 'lang', 'field', 'en-us', 'UnitTest') . '.php';
        $snapshotPath = createPath(__DIR__, '__snapshots__', 'lang', 'field', 'en-us', 'UnitTest') . '.php.snap';
        $this->assertTrue(is_file($filePath));
        $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
    }

    public function testCreateValidateFileSuccessfully()
    {
        $testStubPath = createPath(__DIR__, 'stubs', '_validate') . '.stub';
        $mockStubPath = createPath(base_path(), 'api', 'validate', '_validate') . '.stub';
        makeDir(dirname($mockStubPath));
        copy($testStubPath, $mockStubPath);

        try {
            ModelCreator::file('unit-test', 'Unit Test', 'en-us')->createValidateFile($this->fieldsData);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $filePath = createPath(base_path(), 'api', 'validate', 'UnitTest') . '.php';
        $snapshotPath = createPath(__DIR__, '__snapshots__', 'validate', 'UnitTest') . '.php.modified.snap';
        $this->assertTrue(is_file($filePath));
        $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
    }

    public function testCreateValidateI18nSuccessfully()
    {
        try {
            $langFieldPath = createPath(base_path(), 'api', 'lang', 'field', 'en-us', 'UnitTest') . '.php';
            if (file_exists($langFieldPath)) {
                Lang::load($langFieldPath);
            }
            ModelCreator::file('unit-test', 'Unit Test', 'en-us')->createValidateI18n($this->fieldsData);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $filePath = createPath(base_path(), 'api', 'lang', 'validate', 'en-us', 'UnitTest') . '.php';
        $snapshotPath = createPath(__DIR__, '__snapshots__', 'lang', 'validate', 'en-us', 'UnitTest') . '.php.snap';
        $this->assertTrue(is_file($filePath));
        $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
    }

    public function testCreateAllowConfigSuccessfully()
    {
        $testStubPath = createPath(__DIR__, 'stubs', '_allowFields') . '.stub';
        $mockStubPath = createPath(base_path(), 'config', 'api', 'allowFields', '_allowFields') . '.stub';
        makeDir(dirname($mockStubPath));
        copy($testStubPath, $mockStubPath);

        try {
            ModelCreator::file('unit-test', 'Unit Test', 'en-us')->createAllowConfig($this->fieldsData);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $filePath = createPath(root_path(), 'config', 'api', 'allowFields', 'UnitTest') . '.php';
        $snapshotPath = createPath(__DIR__, '__snapshots__', 'config', 'api', 'allowFields', 'UnitTest') . '.php.snap';
        $this->assertTrue(is_file($filePath));
        $this->assertTrue(matchSnapshot($filePath, $snapshotPath));
    }

    public function testUpdateSuccessfully()
    {
        ModelCreator::file('unit-test', 'Unit Test', 'en-us')->remove(['langField', 'validateModified','validateI18n', 'allowFields']);

        $testStubPath = createPath(__DIR__, 'stubs', '_validate') . '.stub';
        $mockStubPath = createPath(base_path(), 'api', 'validate', '_validate') . '.stub';
        makeDir(dirname($mockStubPath));
        copy($testStubPath, $mockStubPath);

        $testStubPath = createPath(__DIR__, 'stubs', '_allowFields') . '.stub';
        $mockStubPath = createPath(base_path(), 'config', 'api', 'allowFields', '_allowFields') . '.stub';
        makeDir(dirname($mockStubPath));
        copy($testStubPath, $mockStubPath);

        ModelCreator::file('unit-test', 'Unit Test', 'en-us')->update($this->fieldsData);

        $filesPath = [
            createPath(base_path(), 'api', 'lang', 'field', 'en-us', 'UnitTest') . '.php',
            createPath(base_path(), 'api', 'validate', 'UnitTest') . '.php',
            createPath(base_path(), 'api', 'lang', 'validate', 'en-us', 'UnitTest') . '.php',
            createPath(root_path(), 'config', 'api', 'allowFields', 'UnitTest') . '.php'
        ];
        foreach ($filesPath as $path) {
            $this->assertTrue(is_file($path));
        }
    }
}
