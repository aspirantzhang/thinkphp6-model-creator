<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

use think\facade\Db;
use think\Exception;
use aspirantzhang\octopusModelCreator\lib\file\BasicModel;
use aspirantzhang\octopusModelCreator\lib\file\FieldLang;
use aspirantzhang\octopusModelCreator\lib\file\LayoutLang;
use aspirantzhang\octopusModelCreator\lib\file\Validate;
use aspirantzhang\octopusModelCreator\lib\file\ValidateLang;
use aspirantzhang\octopusModelCreator\lib\file\Filter;

class File
{
    protected $config;

    private function getConfig()
    {
        $config = $this->config;
        $config['type'] = $config['type'] ?? 'main';
        return $config;
    }

    public function config(array $config)
    {
        if (
            !isset($config['name']) ||
            empty($config['name']) ||
            !isset($config['title']) ||
            empty($config['title'])
        ) {
            throw new Exception(__('missing required config name and title'));
        }
        $this->config = $config;
        return $this;
    }

    private function getMainTableInfo(int $id)
    {
        $mainTable = Db::name('model')
            ->alias('o')
            ->where('o.id', $id)
            ->leftJoin('model_i18n i', 'o.id = i.original_id')
            ->find();
        if ($mainTable === null) {
            throw new Exception(__('can not find main table'));
        }
        return $mainTable;
    }

    public function checkCategoryTypeConfig()
    {
        $config = $this->getConfig();
        if (
            !isset($config['parentId']) ||
            empty($config['parentId'])
        ) {
            throw new Exception(__('missing required config parentId'));
        }
    }

    public function create()
    {
        $config = $this->getConfig();
        // TODO: refactor to separate method
        if ($config['type'] === 'category') {
            $this->checkCategoryTypeConfig();
            // get main table info using parent id
            $mainTable = $this->getMainTableInfo((int)$config['parentId']);
            // rebuild the controller and model of main model
            (new BasicModel())->init([
                'name' => $mainTable['table_name'],
                'title' => $mainTable['model_title'],
                'type' => 'mainTableOfCategory',
                'categoryTableName' => $config['name'],
            ])->createBasicModelFile(['controller', 'model']);
            // create category model
            (new BasicModel())->init([
                'name' => $config['name'],
                'title' => $config['title'],
                'type' => 'categoryTableOfCategory',
                'mainTableName' => $mainTable['table_name'],
            ])->createBasicModelFile();
            return;
        }
        return (new BasicModel())->init($this->config)->createBasicModelFile();
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
        $allFieldsArray = ModelCreator::helper()->extractAllFields($fieldsData);
        (new FieldLang())->init($this->getConfig())->createFieldLangFile($allFieldsArray);
        (new LayoutLang())->init($this->getConfig())->createLayoutLangFile();
        if ($fieldOptions['handleFieldValidation'] ?? false) {
            (new Validate())->init($this->getConfig())->createValidateFile($allFieldsArray);
            (new ValidateLang())->init($this->getConfig())->createValidateLangFile($allFieldsArray);
        }
        if ($fieldOptions['handleFieldFilter'] ?? false) {
            (new Filter())->init($this->getConfig())->createFilterFile($allFieldsArray);
        }
    }

    public function remove()
    {
        try {
            $config = $this->getConfig();
            if ($config['type'] === 'category') {
                // get main table info using parent id
                $mainTable = $this->getMainTableInfo((int)$config['parentId']);
                (new BasicModel())->init([
                    'name' => $config['name'],
                    'title' => $config['title'],
                    'type' => 'categoryTableOfCategory',
                    'mainTableName' => $mainTable['table_name'],
                ])->removeBasicModelFile();
                // rebuild main model
                (new BasicModel())->init([
                    'name' => $mainTable['table_name'],
                    'title' => $mainTable['model_title'],
                ])->createBasicModelFile(['controller', 'model']);
            } else {
                (new BasicModel())->init($this->getConfig())->removeBasicModelFile();
            }
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
