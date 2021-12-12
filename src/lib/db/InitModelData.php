<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

class InitModelData
{
    private $tableName;
    private $type;
    private $data;

    public function __construct(string $tableName, $type)
    {
        $this->tableName = strtolower($tableName);
        $this->type = (int)$type;
        $this->init();
    }

    public function init()
    {
        $this->data = [
            'layout' => [
                'tableName' => $this->tableName,
                'listAction' => [
                    [
                        'name' => 'quick_edit',
                        'title' => 'Quick Edit',
                        'type' => 'default',
                        'call' => 'modal',
                        'uri' => '/api/' . $this->tableName . '/:id/quickEdit',
                        'method' => 'get'
                    ],
                    [
                        'name' => 'edit',
                        'title' => 'Edit',
                        'type' => 'primary',
                        'call' => 'page',
                        'uri' => '/api/' . $this->tableName . '/:id',
                        'method' => 'get'
                    ],
                    [
                        'name' => 'delete',
                        'title' => 'Delete',
                        'type' => 'default',
                        'call' => 'delete',
                        'uri' => '/api/' . $this->tableName . '/delete',
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
                        'uri' => '/api/' . $this->tableName . '',
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
                        'uri' => '/api/' . $this->tableName . '/:id',
                        'method' => 'put'
                    ]
                ],
                'tableToolbar' => [
                    [
                        'name' => 'add',
                        'title' => 'Add',
                        'type' => 'primary',
                        'call' => 'page',
                        'uri' => '/api/' . $this->tableName . '/add',
                        'method' => 'get'
                    ]
                ],
                'batchToolbar' => [
                    [
                        'name' => 'batch_delete',
                        'title' => 'Batch Delete',
                        'type' => 'danger',
                        'call' => 'delete',
                        'uri' => '/api/' . $this->tableName . '/delete',
                        'method' => 'post'
                    ],
                    [
                        'name' => 'batch_disable',
                        'title' => 'Batch Disable',
                        'type' => 'default',
                        'call' => 'disable',
                        'uri' => '/api/' . $this->tableName . '/disable',
                        'method' => 'post'
                    ],
                    [
                        'name' => 'batch_enable',
                        'title' => 'Batch Enable',
                        'type' => 'default',
                        'call' => 'disable',
                        'uri' => '/api/' . $this->tableName . '/enable',
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
                        'uri' => '/api/' . $this->tableName . '/delete'
                    ],
                    [
                        'name' => 'restore',
                        'title' => 'Restore',
                        'type' => 'default',
                        'call' => 'restore',
                        'uri' => '/api/' . $this->tableName . '/restore',
                        'method' => 'post'
                    ]
                ],
            ],
            'fields' => [
                'options' => [
                    'handleFieldValidation' => '1',
                    'handleFieldFilter' => '1',
                ],
            ]
        ];
    }

    public function typeCategory()
    {
        $parentField = [
            'name' => 'parent_id',
            'title' => 'Parent',
            'type' => 'parent',
            'settings' => [ 'validate' => ['checkParentId'] ],
            'allowHome' => '1',
            'allowRead' => '1',
            'allowSave' => '1',
            'allowUpdate' => '1'
        ];
        $this->data['fields']['sidebars']['parent'][] = $parentField;
    }

    public function getData()
    {
        if ($this->type === 2) {
            $this->typeCategory();
        }
        return (array)$this->data;
    }
}
