<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator;

use think\Service as BaseService;

class Service extends BaseService
{
    public function boot()
    {
        $this->commands([
            'file:create' => command\file\Create::class,
            'file:remove' => command\file\Remove::class,
            'db:create' => command\db\Create::class,
            'db:remove' => command\db\Remove::class,
            'db:deleteReservedTable' => command\db\DeleteTable::class
        ]);
    }
}
