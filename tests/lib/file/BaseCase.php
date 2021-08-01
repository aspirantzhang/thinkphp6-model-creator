<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

if (!function_exists('base_path')) {
    function base_path()
    {
        return createPath(dirname(__DIR__, 3), 'runtime');
    }
}
if (!function_exists('root_path')) {
    function root_path()
    {
        return createPath(dirname(__DIR__, 3), 'runtime');
    }
}
class BaseCase extends \aspirantzhang\octopusModelCreator\TestCase
{
    protected $fieldsData;

    protected function setUp(): void
    {
        $this->fieldsData = [
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
        parent::setUp();
    }
}
