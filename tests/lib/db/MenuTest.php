<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

use think\Exception;

class MenuTest extends BaseCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testCreateMenu()
    {
        $id = (new Menu())->init('menu-test', 'Menu Test')->createMenu('menu_path');
        $this->assertTrue(true);
        return $id;
    }

    /**
    * @depends testCreateMenu
    */
    public function testCreateChildrenMenus($id)
    {
        (new Menu())->init('menu-test', 'Menu Test')->createChildrenMenus($id);
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
