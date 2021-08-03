<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

use think\Service as BaseService;

class Service extends BaseService
{
    public function boot()
    {
        $this->commands([
            'misc:deleteTable' => command\misc\DeleteTable::class
        ]);
    }
}
