<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib;

class ValidateTest extends \aspirantzhang\octopusModelCreator\TestCase
{
    public function testValidateBuildSuccessfully()
    {
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
        $actual = (new Validate('unit-test', $fieldsData))->getData();
        $expect = [
            'rules' => [
                'id' => 'require|number',
                'ids' => 'require|numberArray',
                'status' => 'numberTag',
                'page' => 'number',
                'per_page' => 'number',
                'create_time' => 'require|dateTimeRange',
                'unit-test@nickname' => 'require|length:4,32',
                'unit-test@gender' => 'require',
                'unit-test@married' => 'require',
            ],
            'messages' => [
                'id.require' => 'id#require',
                'id.number' => 'id#number',
                'ids.require' => 'ids#require',
                'ids.numberArray' => 'ids#numberArray',
                'status.numberTag' => 'status#numberTag',
                'page.number' => 'page#number',
                'per_page.number' => 'per_page#number',
                'create_time.require' => 'create_time#require',
                'create_time.dateTimeRange' => 'create_time#dateTimeRange',
                'nickname.require' => 'unit-test@nickname#require',
                'nickname.length:4,32' => 'unit-test@nickname#length:4,32',
                'gender.require' => 'unit-test@gender#require',
                'married.require' => 'unit-test@married#require',
            ],
            'scenes' => [
                'save' => ['create_time', 'status', 'nickname', 'gender', 'married'],
                'update' => ['id', 'create_time', 'status', 'nickname', 'gender', 'married'],
                'read' => ['id'],
                'delete' => ['ids'],
                'restore' => ['ids'],
                'i18n' => ['id'],
                'i18n_update' => ['id'],
                'add' => [''],
                'home' => ['nickname','gender','married'],
                'homeExclude' => ['nickname','gender','married'],
            ],
        ];
        $this->assertEqualsCanonicalizing($expect, $actual);
    }
}
