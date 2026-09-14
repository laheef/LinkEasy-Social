<?php
namespace App\Services;
use App\Database;
final class DashboardService
{
    public static function subscription(array $user): ?array {
        $q=Database::pdo()->prepare("SELECT id,plan,status,billing_cycle,current_period_end FROM les_subscriptions WHERE user_id=? ORDER BY CASE WHEN plan=? AND status='active' THEN 0 ELSE 1 END,id DESC LIMIT 1");
        $q->execute([(int)$user['id'],$user['plan']]);return $q->fetch()?:null;
    }
}
