<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

use think\Exception;
use aspirantzhang\octopusModelCreator\lib\file\BasicModel;
use aspirantzhang\octopusModelCreator\lib\file\FieldLang;
use aspirantzhang\octopusModelCreator\lib\file\LayoutLang;
use aspirantzhang\octopusModelCreator\lib\file\Validate;
use aspirantzhang\octopusModelCreator\lib\file\ValidateLang;
use aspirantzhang\octopusModelCreator\lib\file\AllowField;

class File
{
    protected $tableName;
    protected $modelTitle;

    public function init(string $tableName, string $modelTitle)
    {
        $this->tableName = $tableName;
        $this->modelTitle = $modelTitle;
        return $this;
    }
    
    public function create()
    {
        try {
            (new BasicModel())->init($this->tableName, $this->modelTitle)->createBasicModelFile();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function update($fieldsData)
    {
        try {
            (new FieldLang())->init($this->tableName, $this->modelTitle)->createFieldLangFile($fieldsData);
            (new LayoutLang())->init($this->tableName, $this->modelTitle)->createLayoutLangFile();
            (new Validate())->init($this->tableName, $this->modelTitle)->createValidateFile($fieldsData);
            (new ValidateLang())->init($this->tableName, $this->modelTitle)->createValidateLangFile($fieldsData);
            (new AllowField())->init($this->tableName, $this->modelTitle)->createAllowFieldsFile($fieldsData);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function remove()
    {
        try {
            (new BasicModel())->init($this->tableName, $this->modelTitle)->removeBasicModelFile();
            (new FieldLang())->init($this->tableName, $this->modelTitle)->removeFieldLangFile();
            (new LayoutLang())->init($this->tableName, $this->modelTitle)->removeLayoutLangFile();
            (new Validate())->init($this->tableName, $this->modelTitle)->removeValidateFile();
            (new ValidateLang())->init($this->tableName, $this->modelTitle)->removeValidateLangFile();
            (new AllowField())->init($this->tableName, $this->modelTitle)->removeAllowFieldsFile();
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
