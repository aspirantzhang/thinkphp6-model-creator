<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

use think\Exception;

class InitModelDataTest extends BaseCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testInitDataOfTypeMain()
    {
        $actual = (new InitModelData('Main_Model', 1))->getData();
        $expected  = [
            'layout' => [
                'tableName' => 'main_model'
            ],
            'fields' => [
                'options' => [
                    'handleFieldValidation' => '1',
                    "handleFieldFilter" => '1',
                ],
            ]
        ];
        $this->assertEqualsCanonicalizing($actual, $expected);
    }
    public function testInitDataOfTypeCategory()
    {
        $actual = (new InitModelData('Category_Model', 2))->getData();
        $expected  = [
            'layout' => [
                'tableName' => 'category_model'
            ],
            'fields' => [
                'options' => [
                    'handleFieldValidation' => '1',
                    "handleFieldFilter" => '1',
                ],
                'sidebars' => [
                    'parent' => [
                        [
                            'name' => 'parent_id',
                            'title' => 'Parent',
                            'type' => 'parent',
                            'settings' => [ 'validate' => ['checkParentId'] ],
                            'allowHome' => '1',
                            'allowRead' => '1',
                            'allowSave' => '1',
                            'allowUpdate' => '1'
                        ]
                    ],
                ]
            ]
        ];
        $this->assertEqualsCanonicalizing($actual, $expected);
    }
}
