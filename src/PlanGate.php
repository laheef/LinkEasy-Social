<?php

namespace App;

/**
 * Central plan-permission system. Limits are read from config('pricing')
 * so the landing page, signup defaults and feature gates never disagree.
 *
 * Usage:
 *   if (!PlanGate::allows($user, 'max_social_accounts', $currentCount)) {
 *       flash('warning', "You've reached your current plan limit.");
 *       redirect(url('pricing'));   // /#pricing upgrade section
 *   }
 */
final class PlanGate
{
    public static function limits(string $plan): array
    {
        $plans = config('pricing.plans');
        if ($plan === 'managed') {
            // Active paid plan inherits managed limits regardless of monthly/yearly.
            return $plans['managed']['limits'] ?? [];
        }
        return $plans[$plan]['limits'] ?? $plans['free']['limits'];
    }

    /**
     * @param numeric|null $current current usage (null for boolean-style gates)
     * @param int $add amount being added (default 1)
     */
    public static function allows(array $user, string $limit, float|int|null $current = null, int $add = 1): bool
    {
        $limits = self::limits((string) ($user['plan'] ?? 'free'));
        if (!array_key_exists($limit, $limits)) {
            return false; // Fail closed on unknown/typoed gates.
        }
        $max = $limits[$limit];
        if ($max === null) {
            return true; // unlimited
        }
        if ($current === null) {
            return (bool) $max;
        }
        return $current >= 0 && $add >= 0 && ($current + $add) <= $max;
    }

    /**
     * Authorization check every protected resource must pass.
     * Trusts the session user, never a user_id/project_id from the client.
     */
    public static function owns(array $user, array $resource, string $ownerColumn = 'user_id'): bool
    {
        return isset($resource[$ownerColumn]) && (int) $resource[$ownerColumn] === (int) $user['id'];
    }
}
