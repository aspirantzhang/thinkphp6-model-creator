<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

use think\Exception;

class MenuTest extends BaseCase
{
    protected $config;

    protected function setUp(): void
    {
        parent::setUp();
        $this->config = [
            'name' => 'menu-test',
            'title' => 'Menu Test',
        ];
    }

    public function testCreateMenu()
    {
        $id = (new Menu())->init($this->config)->createMenu('menu_path');
        $this->assertTrue(true);
        return $id;
    }

    /**
    * @depends testCreateMenu
    */
    public function testCreateChildrenMenus($id)
    {
        (new Menu())->init($this->config)->createChildrenMenus($id);
        $this->assertTrue(true);
        return $id;
    }
    /**
    * @depends testCreateChildrenMenus
    */
    public function testRemoveMenuSuccessfully($id)
    {
        (new Menu())->removeMenus($id);
        $this->assertTrue(true);
    }
}
