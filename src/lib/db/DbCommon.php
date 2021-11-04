<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

use think\helper\Str;

class DbCommon
{
    protected $tableName;
    protected $routeName;
    protected $modelName;
    protected $instanceName;
    protected $modelTitle;
    protected $modelType;

    public function init(array $config)
    {
        $this->tableName = $config['name'];
        $this->routeName = $config['name'];
        $this->modelName = Str::studly($config['name']);
        $this->instanceName = Str::camel($config['name']);
        $this->modelTitle = $config['title'];
        $this->modelType = $config['type'] ?? 'main';
        return $this;
    }
}
