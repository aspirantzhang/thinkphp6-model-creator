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
        try {
            $id = (new Menu())->init('menu-test', 'Menu Test')->createMenu('menu_path');
            $this->assertTrue(true);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $id;
    }

    /**
    * @depends testCreateMenu
    */
    public function testCreateChildrenMenus($id)
    {
        try {
            (new Menu())->init('menu-test', 'Menu Test')->createChildrenMenus($id);
            $this->assertTrue(true);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        return $id;
    }
    /**
    * @depends testCreateChildrenMenus
    */
    public function testRemoveMenuSuccessfully($id)
    {
        try {
            (new Menu())->removeMenus($id);
            $this->assertTrue(true);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
