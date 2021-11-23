<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

use think\facade\Db as ThinkDb;
use think\Exception;
use aspirantzhang\octopusModelCreator\TestCase;

class FileTest extends TestCase
{
    protected $file;
    protected $snapPath;
    protected $prodStubPath;
    protected $testStubPath;
    protected $fileTypes;

    protected function setUp(): void
    {
        $this->snapPath = createPath(__DIR__, 'snapshots', 'lib', 'file', 'BasicModel');
        $this->fileTypes = ['controller', 'model', 'view', 'logic', 'service', 'route', 'validate'];
        parent::setUp();
    }

    public function testSingleMainCreate()
    {
        $config = [
            'name' => 'new-model',
            'title' => 'New Model',
        ];
        ModelCreator::file()->config($config)->create();

        foreach ($this->fileTypes as $type) {
            $filePath = createPath(base_path(), 'api', $type, 'NewModel') . '.php';
            $this->assertTrue($this->fileSystem->exists($filePath));
        }
    }

    /**
    * @depends testSingleMainCreate
    */
    public function testSingleMainUpdate()
    {
        $config = [
            'name' => 'new-model',
            'title' => 'New Model',
        ];
        ModelCreator::file()->config($config)->update($this->getDemo('default-field')['data'], [
            'handleFieldValidation' => true,
            'handleFieldFilter' => true
        ]);
        $filePaths = [];
        $filePaths[] = createPath(base_path(), 'api', 'lang', 'field', 'en-us', 'NewModel') . '.php';
        $filePaths[] = createPath(base_path(), 'api', 'lang', 'layout', 'en-us', 'NewModel') . '.php';
        $filePaths[] = createPath(base_path(), 'api', 'validate', 'NewModel') . '.php';
        $filePaths[] = createPath(base_path(), 'api', 'lang', 'validate', 'en-us', 'NewModel') . '.php';
        $filePaths[] = createPath(root_path(), 'config', 'api', 'model', 'NewModel') . '.php';
        foreach ($filePaths as $filePath) {
            $this->assertTrue($this->fileSystem->exists($filePath));
        }
    }

    /**
    * @depends testSingleMainUpdate
    */
    public function testSingleMainRemove()
    {
        $config = [
            'name' => 'new-model',
            'title' => 'New Model',
        ];
        ModelCreator::file()->config($config)->remove();
        $filePaths = array_map(function ($type) {
            return createPath(base_path(), 'api', $type, 'NewModel') . '.php';
        }, $this->fileTypes);
        $filePaths[] = createPath(base_path(), 'api', 'lang', 'field', 'en-us', 'NewModel') . '.php';
        $filePaths[] = createPath(base_path(), 'api', 'lang', 'layout', 'en-us', 'NewModel') . '.php';
        $filePaths[] = createPath(base_path(), 'api', 'validate', 'NewModel') . '.php';
        $filePaths[] = createPath(base_path(), 'api', 'lang', 'validate', 'en-us', 'NewModel') . '.php';
        $filePaths[] = createPath(root_path(), 'config', 'api', 'model', 'NewModel') . '.php';
        foreach ($filePaths as $filePath) {
            $this->assertFalse($this->fileSystem->exists($filePath));
        }
    }

    private function createCategoryTestingTable()
    {
        ThinkDb::execute('DROP TABLE IF EXISTS `model`, `model_i18n`;');
        ThinkDb::execute(<<<END
CREATE TABLE `model` (
`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
`table_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
`type` tinyint(4) unsigned NOT NULL DEFAULT 1,
`parent_id` int(11) unsigned NOT NULL DEFAULT 0,
`data` text COLLATE utf8mb4_unicode_ci NOT NULL,
`rule_id` int(11) unsigned NOT NULL DEFAULT 0,
`menu_id` int(11) unsigned NOT NULL DEFAULT 0,
`create_time` datetime NOT NULL,
`update_time` datetime NOT NULL,
`delete_time` datetime DEFAULT NULL,
`status` tinyint(1) NOT NULL DEFAULT 1,
PRIMARY KEY (`id`),
UNIQUE KEY `table_name` (`table_name`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
END
        );
        ThinkDb::execute(<<<END
CREATE TABLE `model_i18n` (
 `_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
 `original_id` int(11) unsigned NOT NULL,
 `lang_code` char(5) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
 `model_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
 `translate_time` datetime DEFAULT NULL,
 PRIMARY KEY (`_id`),
 UNIQUE KEY `original_id` (`original_id`,`lang_code`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
END
        );

        $currentTime = date('Y-m-d H:i:s');
        ThinkDb::name('model')->insertAll([
            [
                'id' => 1,
                'table_name' => 'main_table',
                'type' => 1,
                'parent_id' => 0,
                'data' => '',
                'create_time' => $currentTime,
                'update_time' => $currentTime,
            ],
        ]);

        ThinkDb::name('model_i18n')->insertAll([
            [
                '_id' => 1,
                'original_id' => 1,
                'lang_code' => 'en-us',
                'model_title' => 'Main Model',
            ],
        ]);
    }

    public function testCategoryModelCreate()
    {
        $this->createCategoryTestingTable();
        $config = [
            'name' => 'category_table',
            'title' => 'Category Model',
            'type' => 'category',
            'parentId' => 1,
        ];
        ModelCreator::file()->config($config)->create();
        // TODO: improve testing, check all file existing
        $this->assertTrue(true);
        return $config;
    }

    /**
    * @depends testCategoryModelCreate
    */
    public function testCategoryModelRemove($config)
    {
        ModelCreator::file()->config($config)->remove();
        $filePaths = array_map(function ($type) {
            return createPath(base_path(), 'api', $type, 'CategoryTable') . '.php';
        }, $this->fileTypes);
        $filePaths[] = createPath(base_path(), 'api', 'lang', 'field', 'en-us', 'CategoryTable') . '.php';
        $filePaths[] = createPath(base_path(), 'api', 'lang', 'layout', 'en-us', 'CategoryTable') . '.php';
        $filePaths[] = createPath(base_path(), 'api', 'validate', 'CategoryTable') . '.php';
        $filePaths[] = createPath(base_path(), 'api', 'lang', 'validate', 'en-us', 'CategoryTable') . '.php';
        $filePaths[] = createPath(root_path(), 'config', 'api', 'model', 'CategoryTable') . '.php';
        foreach ($filePaths as $filePath) {
            $this->assertFalse($this->fileSystem->exists($filePath));
        }
        // main model rebuild check
        $mainControllerPath = createPath(base_path(), 'api', 'controller', 'MainTable') . '.php';
        $mainControllerSnap = createPath($this->snapPath, 'api', 'controller', 'MainTableAfterRemoveCategory') . '.php.snap';
        $this->assertTrue(matchSnapshot($mainControllerPath, $mainControllerSnap));
        $mainModelPath = createPath(base_path(), 'api', 'model', 'MainTable') . '.php';
        $mainModelSnap = createPath($this->snapPath, 'api', 'model', 'MainTableAfterRemoveCategory') . '.php.snap';
        $this->assertTrue(matchSnapshot($mainModelPath, $mainModelSnap));
    }
}
