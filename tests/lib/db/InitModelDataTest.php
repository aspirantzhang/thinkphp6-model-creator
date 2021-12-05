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
                'tabs' => [
                    'basic' => [
                        [
                            'name' => 'title',
                            'title' => 'Title',
                            'type' => 'input',
                            'settings' => [
                              'validate' => ['require', 'length'],
                              'options' => [ 'length' => [ 'min' => '1', 'max' => '255' ] ]
                            ],
                            'titleField' => '1',
                            'allowHome' => '1',
                            'allowRead' => '1',
                            'allowSave' => '1',
                            'allowUpdate' => '1',
                            'allowTranslate' => '1'
                        ],
                        [
                            'name' => 'pathname',
                            'title' => 'Path',
                            'type' => 'input',
                            'settings' => [
                              'validate' => ['length'],
                              'options' => [ 'length' => [ 'min' => '0', 'max' => '255' ] ],
                              'display' => ['hideInColumn']
                            ],
                            'allowHome' => '1',
                            'allowRead' => '1',
                            'allowSave' => '1',
                            'allowUpdate' => '1'
                        ]
                    ]
                ],
                'sidebars' => [
                    'basic' => [
                        [
                            'name' => 'list_order',
                            'title' => 'Order',
                            'type' => 'number',
                            'settings' => [ 'display' => ['listSorter'], 'validate' => ['number'] ],
                            'allowHome' => '1',
                            'allowRead' => '1',
                            'allowSave' => '1',
                            'allowUpdate' => '1'
                        ]
                    ]
                ]
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
                'tabs' => [
                    'basic' => [
                        [
                            'name' => 'title',
                            'title' => 'Title',
                            'type' => 'input',
                            'settings' => [
                              'validate' => ['require', 'length'],
                              'options' => [ 'length' => [ 'min' => '1', 'max' => '255' ] ]
                            ],
                            'titleField' => '1',
                            'allowHome' => '1',
                            'allowRead' => '1',
                            'allowSave' => '1',
                            'allowUpdate' => '1',
                            'allowTranslate' => '1'
                        ],
                        [
                            'name' => 'pathname',
                            'title' => 'Path',
                            'type' => 'input',
                            'settings' => [
                              'validate' => ['length'],
                              'options' => [ 'length' => [ 'min' => '0', 'max' => '255' ] ],
                              'display' => ['hideInColumn']
                            ],
                            'allowHome' => '1',
                            'allowRead' => '1',
                            'allowSave' => '1',
                            'allowUpdate' => '1'
                        ]
                    ]
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
                    'basic' => [
                        [
                            'name' => 'list_order',
                            'title' => 'Order',
                            'type' => 'number',
                            'settings' => [ 'display' => ['listSorter'], 'validate' => ['number'] ],
                            'allowHome' => '1',
                            'allowRead' => '1',
                            'allowSave' => '1',
                            'allowUpdate' => '1'
                        ]
                    ]
                ]
            ]
        ];
        $this->assertEqualsCanonicalizing($actual, $expected);
    }
}
