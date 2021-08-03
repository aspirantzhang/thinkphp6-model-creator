<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

use think\Exception;

class FieldTest extends BaseCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testFieldsHandler()
    {
        (new Table())->init('field-test', 'Field Test')->createModelTable();

        $fieldsData = [
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
        $fieldsDataUpdate = [
            [
                "name" => "nickname",
                "title" => "Nick Name",
                "type" => "longtext",
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
            ],
            [
                "name" => "age",
                "title" => "Age",
                "type" => "number",
            ],
            [
                "name" => "foo_time",
                "title" => "Foo Time",
                "type" => "datetime",
            ],
        ];
        $reservedFields = [
            'id',
            'create_time',
            'update_time',
            'delete_time',
            'status',
            '_id',
            'original_id',
            'lang_code',
            'translate_time'
        ];
        try {
            (new Field())->init('field-test', 'Field Test')->fieldsHandler($fieldsData, ['nickname', 'gender', 'married'], $reservedFields);
            (new Field())->init('field-test', 'Field Test')->fieldsHandler($fieldsDataUpdate, ['nickname', 'gender', 'age', 'foo_time'], $reservedFields);
            $this->assertTrue(true);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
