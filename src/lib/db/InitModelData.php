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
