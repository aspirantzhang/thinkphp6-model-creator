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
                'tableName' => 'main_model',
                'listAction' => [
                    [
                        'name' => 'quick_edit',
                        'title' => 'Quick Edit',
                        'type' => 'default',
                        'call' => 'modal',
                        'uri' => '/api/main_model/:id/quickEdit',
                        'method' => 'get'
                    ],
                    [
                        'name' => 'edit',
                        'title' => 'Edit',
                        'type' => 'primary',
                        'call' => 'page',
                        'uri' => '/api/main_model/:id',
                        'method' => 'get'
                    ],
                    [
                        'name' => 'delete',
                        'title' => 'Delete',
                        'type' => 'default',
                        'call' => 'delete',
                        'uri' => '/api/main_model/delete',
                        'method' => 'post'
                    ]
                ],
                'addAction' => [
                    [
                        'name' => 'reset',
                        'title' => 'Reset',
                        'type' => 'dashed',
                        'call' => 'reset',
                        'method' => 'get'
                    ],
                    [
                        'name' => 'cancel',
                        'title' => 'Cancel',
                        'type' => 'default',
                        'call' => 'cancel',
                        'method' => 'get'
                    ],
                    [
                        'name' => 'submit',
                        'title' => 'Submit',
                        'type' => 'primary',
                        'call' => 'submit',
                        'uri' => '/api/main_model',
                        'method' => 'post'
                    ]
                ],
                'editAction' => [
                    [
                        'name' => 'cancel',
                        'title' => 'Cancel',
                        'type' => 'default',
                        'call' => 'cancel',
                        'method' => 'get'
                    ],
                    [
                        'name' => 'submit',
                        'title' => 'Submit',
                        'type' => 'primary',
                        'call' => 'submit',
                        'uri' => '/api/main_model/:id',
                        'method' => 'put'
                    ]
                ],
                'tableToolbar' => [
                    [
                        'name' => 'add',
                        'title' => 'Add',
                        'type' => 'primary',
                        'call' => 'page',
                        'uri' => '/api/main_model/add',
                        'method' => 'get'
                    ]
                ],
                'batchToolbar' => [
                    [
                        'name' => 'batch_delete',
                        'title' => 'Batch Delete',
                        'type' => 'danger',
                        'call' => 'delete',
                        'uri' => '/api/main_model/delete',
                        'method' => 'post'
                    ],
                    [
                        'name' => 'batch_disable',
                        'title' => 'Batch Disable',
                        'type' => 'default',
                        'call' => 'disable',
                        'uri' => '/api/main_model/disable',
                        'method' => 'post'
                    ],
                    [
                        'name' => 'batch_enable',
                        'title' => 'Batch Enable',
                        'type' => 'default',
                        'call' => 'disable',
                        'uri' => '/api/main_model/enable',
                        'method' => 'post'
                    ]
                ],
                'batchToolbarTrashed' => [
                    [
                        'name' => 'delete_permanently',
                        'title' => 'Delete Permanently',
                        'type' => 'danger',
                        'call' => 'deletePermanently',
                        'method' => 'post',
                        'uri' => '/api/main_model/delete'
                    ],
                    [
                        'name' => 'restore',
                        'title' => 'Restore',
                        'type' => 'default',
                        'call' => 'restore',
                        'uri' => '/api/main_model/restore',
                        'method' => 'post'
                    ]
                ],
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
                'tableName' => 'category_model',
                'listAction' => [
                    [
                        'name' => 'quick_edit',
                        'title' => 'Quick Edit',
                        'type' => 'default',
                        'call' => 'modal',
                        'uri' => '/api/category_model/:id/quickEdit',
                        'method' => 'get'
                    ],
                    [
                        'name' => 'edit',
                        'title' => 'Edit',
                        'type' => 'primary',
                        'call' => 'page',
                        'uri' => '/api/category_model/:id',
                        'method' => 'get'
                    ],
                    [
                        'name' => 'delete',
                        'title' => 'Delete',
                        'type' => 'default',
                        'call' => 'delete',
                        'uri' => '/api/category_model/delete',
                        'method' => 'post'
                    ]
                ],
                'addAction' => [
                    [
                        'name' => 'reset',
                        'title' => 'Reset',
                        'type' => 'dashed',
                        'call' => 'reset',
                        'method' => 'get'
                    ],
                    [
                        'name' => 'cancel',
                        'title' => 'Cancel',
                        'type' => 'default',
                        'call' => 'cancel',
                        'method' => 'get'
                    ],
                    [
                        'name' => 'submit',
                        'title' => 'Submit',
                        'type' => 'primary',
                        'call' => 'submit',
                        'uri' => '/api/category_model',
                        'method' => 'post'
                    ]
                ],
                'editAction' => [
                    [
                        'name' => 'cancel',
                        'title' => 'Cancel',
                        'type' => 'default',
                        'call' => 'cancel',
                        'method' => 'get'
                    ],
                    [
                        'name' => 'submit',
                        'title' => 'Submit',
                        'type' => 'primary',
                        'call' => 'submit',
                        'uri' => '/api/category_model/:id',
                        'method' => 'put'
                    ]
                ],
                'tableToolbar' => [
                    [
                        'name' => 'add',
                        'title' => 'Add',
                        'type' => 'primary',
                        'call' => 'page',
                        'uri' => '/api/category_model/add',
                        'method' => 'get'
                    ]
                ],
                'batchToolbar' => [
                    [
                        'name' => 'batch_delete',
                        'title' => 'Batch Delete',
                        'type' => 'danger',
                        'call' => 'delete',
                        'uri' => '/api/category_model/delete',
                        'method' => 'post'
                    ],
                    [
                        'name' => 'batch_disable',
                        'title' => 'Batch Disable',
                        'type' => 'default',
                        'call' => 'disable',
                        'uri' => '/api/category_model/disable',
                        'method' => 'post'
                    ],
                    [
                        'name' => 'batch_enable',
                        'title' => 'Batch Enable',
                        'type' => 'default',
                        'call' => 'disable',
                        'uri' => '/api/category_model/enable',
                        'method' => 'post'
                    ]
                ],
                'batchToolbarTrashed' => [
                    [
                        'name' => 'delete_permanently',
                        'title' => 'Delete Permanently',
                        'type' => 'danger',
                        'call' => 'deletePermanently',
                        'method' => 'post',
                        'uri' => '/api/category_model/delete'
                    ],
                    [
                        'name' => 'restore',
                        'title' => 'Restore',
                        'type' => 'default',
                        'call' => 'restore',
                        'uri' => '/api/category_model/restore',
                        'method' => 'post'
                    ]
                ],
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
