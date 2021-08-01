<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

use think\facade\Db;
use think\facade\Lang;

class Rule extends DbCommon
{
    public function createRule(string $ruleTitle, int $parentId = 0, string $rulePath = '', string $lang = null): int
    {
        $currentTime = date("Y-m-d H:i:s");
        $lang = $lang ?? Lang::getLangSet();
        try {
            $ruleId = Db::name('auth_rule')->insertGetId([
                'parent_id' => $parentId,
                'rule_path' => $rulePath,
                'create_time' => $currentTime,
                'update_time' => $currentTime,
            ]);
            Db::name('auth_rule_i18n')->insert([
                'original_id' => $ruleId,
                'lang_code' => $lang,
                'rule_title' => $ruleTitle,
                'translate_time' => $currentTime
            ]);
        } catch (\Exception $e) {
            throw new \Exception(__('failed to create rule', ['ruleTitle' => $ruleTitle]));
        }
        return (int)$ruleId;
    }
}
