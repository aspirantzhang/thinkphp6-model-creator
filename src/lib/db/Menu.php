<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

use think\facade\Db;
use think\facade\Lang;

class Menu extends DbCommon
{
    public function createMenu(string $menuPath, string $menuTitle = null, int $parentId = 0, string $lang = null, $addition = []): int
    {
        $menuTitle = $menuTitle ?? $this->modelTitle . __('list');
        $currentTime = date("Y-m-d H:i:s");
        $lang = $lang ?? Lang::getLangSet();
        try {
            $menuId = Db::name('menu')->insertGetId(array_merge([
                'parent_id' => $parentId,
                'icon' => 'icon-project',
                'path' => $menuPath,
                'create_time' => $currentTime,
                'update_time' => $currentTime,
            ], $addition));
            Db::name('menu_i18n')->insert([
                'original_id' => $menuId,
                'lang_code' => $lang,
                'menu_title' => $menuTitle,
                'translate_time' => $currentTime
            ]);
        } catch (\Exception $e) {
            throw new \Exception(__('failed to create menu', ['menuTitle' => $menuTitle]));
        }
        return (int)$menuId;
    }

    public function createChildrenMenus(int $parentMenuId, string $lang = null)
    {
        $lang = $lang ?? Lang::getLangSet();
        $childrenMenus = [
            ['menu_title' => $this->modelTitle . __('add'), 'path' => '/basic-list/api/' . $this->tableName . '/add', 'hide_in_menu' => 1],
            ['menu_title' => $this->modelTitle . __('edit'), 'path' => '/basic-list/api/' . $this->tableName . '/:id', 'hide_in_menu' => 1],
        ];
        $childrenIds = [];
        try {
            foreach ($childrenMenus as $menu) {
                $addition = $menu;
                unset($addition['menu_title'], $addition['path']);
                $childrenIds[] = $this->createMenu($menu['path'], $menu['menu_title'], $parentMenuId, $lang, $addition);
            }
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
        return $childrenIds;
    }

    public function removeMenus(int $id)
    {
        try {
            $allRulesData = Db::table('menu')->where('status', 1)->select()->toArray();
            $allIds = array_merge([$id], searchDescendantValueAggregation('id', 'id', $id, arrayToTree($allRulesData)));
            Db::table('menu')->whereIn('id', $allIds)->delete();
            Db::table('menu_i18n')->whereIn('original_id', $allIds)->delete();
        } catch (\Exception $e) {
            throw new \Exception(__('failed to remove menus'));
        }
    }
}
