<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

use think\Exception;
use aspirantzhang\octopusModelCreator\lib\file\BasicModel;
use aspirantzhang\octopusModelCreator\lib\file\FieldLang;
use aspirantzhang\octopusModelCreator\lib\file\LayoutLang;
use aspirantzhang\octopusModelCreator\lib\file\Validate;
use aspirantzhang\octopusModelCreator\lib\file\ValidateLang;
use aspirantzhang\octopusModelCreator\lib\file\Filter;

class File
{
    protected $tableName;
    protected $modelTitle;
    protected $type;
    protected $config;

    private function checkRequiredConfig()
    {
        if (
            !isset($this->config['name']) ||
            empty($this->config['name']) ||
            !isset($this->config['title']) ||
            empty($this->config['title'])
        ) {
            throw new Exception(__('missing required config name and title'));
        }
    }

    public function config(array $config)
    {
        $this->config = $config;
        $this->checkRequiredConfig();
        return $this;
    }

    public function getConfig()
    {
        $this->checkRequiredConfig();
        return $this->config;
    }

    public function create()
    {
        try {
            (new BasicModel())->init($this->getConfig())->createBasicModelFile();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Update model files
     * @param array $fieldsData
     * @param array $fieldOptions handling options
     * - handleFieldValidation: default false
     * - handleFieldFilter : default false
     * @return void
     */
    public function update(array $fieldsData, array $fieldOptions = [])
    {
        try {
            (new FieldLang())->init($this->getConfig())->createFieldLangFile($fieldsData);
            (new LayoutLang())->init($this->getConfig())->createLayoutLangFile();
            if ($fieldOptions['handleFieldValidation'] ?? false) {
                (new Validate())->init($this->getConfig())->createValidateFile($fieldsData);
                (new ValidateLang())->init($this->getConfig())->createValidateLangFile($fieldsData);
            }
            if ($fieldOptions['handleFieldFilter'] ?? false) {
                (new Filter())->init($this->getConfig())->createFilterFile($fieldsData);
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function remove()
    {
        try {
            (new BasicModel())->init($this->getConfig())->removeBasicModelFile();
            (new FieldLang())->init($this->getConfig())->removeFieldLangFile();
            (new LayoutLang())->init($this->getConfig())->removeLayoutLangFile();
            (new Validate())->init($this->getConfig())->removeValidateFile();
            (new ValidateLang())->init($this->getConfig())->removeValidateLangFile();
            (new Filter())->init($this->getConfig())->removeFilterFile();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
