<?php

declare(strict_types=1);

namespace aspirantzhang\octopusModelCreator\lib\db;

use think\facade\Db;

class GroupRule extends DbCommon
{
    public function addRulesToGroup(array $ruleIds, int $groupId = 1)
    {
        $data = [];
        foreach ($ruleIds as $ruleId) {
            $data[] = ['group_id' => $groupId, 'rule_id' => $ruleId];
        }
        try {
            Db::name('auth_group_rule')->insertAll($data);
        } catch (\Exception $e) {
            throw new \Exception(__('failed to add rules to group'));
        }
    }
}
