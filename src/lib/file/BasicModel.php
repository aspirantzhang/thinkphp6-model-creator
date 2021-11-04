<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\file;

use think\Exception;

class BasicModel extends FileCommon
{
    protected $fileTypes = ['controller', 'model', 'view', 'logic', 'service', 'route', 'validate'];

    public function createBasicModelFile(array $fileTypes = null)
    {
        $fileTypes = $fileTypes ?? $this->fileTypes;
        $replaceCondition = [
            '{{ tableName }}' => $this->tableName,
            '{{ routeName }}' => $this->routeName,
            '{{ modelName }}' => $this->modelName,
            '{{ instanceName }}' => $this->instanceName,
        ];
        if ($this->modelType === 'category') {
            $replaceCondition = array_merge($replaceCondition, [
                '{{ withRelationString }}' => $this->getWithRelation('string'),
            ]);
        }
        try {
            foreach ($fileTypes as $type) {
                $targetPath = createPath($this->appPath, 'api', $type, $this->modelName) . '.php';
                $sourcePath = $this->getStubPath('BasicModel', $type);
                $this->replaceAndWrite($sourcePath, $targetPath, function ($content) use ($replaceCondition) {
                    return strtr($content, $replaceCondition);
                });
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function removeBasicModelFile(array $fileTypes = null)
    {
        $fileTypes = $fileTypes ?? $this->fileTypes;
        $filePaths = array_map(function ($type) {
            return createPath($this->appPath, 'api', $type, $this->modelName) . '.php';
        }, $fileTypes);
        $this->fileSystem->remove($filePaths);
    }
}
