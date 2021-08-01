<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

use think\helper\Str;

class DbCommon
{
    protected $tableName;
    protected $routeName;
    protected $modelName;
    protected $modelTitle;

    public function init($tableName, $modelTitle)
    {
        $this->tableName = $tableName;
        $this->routeName = $tableName;
        $this->modelName = Str::studly($tableName);
        $this->instanceName = Str::camel($tableName);
        $this->modelTitle = $modelTitle;
        return $this;
    }
}
