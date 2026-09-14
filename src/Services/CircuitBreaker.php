<?php
namespace App\Services;
use App\Database;
/** Shared cooldown; after an outage only one half-open probe is admitted. */
final class CircuitBreaker
{
    public static function enter(string $provider): void {
        $ok=Database::transaction(function($pdo)use($provider){
            $insert=Database::driver()==='mysql'?"INSERT INTO les_circuits(provider,failures,open_until,probe_until) VALUES (?,0,0,0) ON DUPLICATE KEY UPDATE provider=VALUES(provider)":"INSERT OR IGNORE INTO les_circuits(provider,failures,open_until,probe_until) VALUES (?,0,0,0)";
            $pdo->prepare($insert)->execute([$provider]);
            $q=$pdo->prepare('SELECT * FROM les_circuits WHERE provider=?'.Database::forUpdate());$q->execute([$provider]);$row=$q->fetch();$now=time();
            if((int)$row['open_until']>$now || (int)$row['probe_until']>$now)return false;
            if((int)$row['failures']>=5)$pdo->prepare('UPDATE les_circuits SET probe_until=? WHERE provider=?')->execute([$now+45,$provider]);
            return true;
        });if(!$ok)throw new \RuntimeException('Provider temporarily unavailable.');
    }
    public static function success(string $provider): void {Database::pdo()->prepare('UPDATE les_circuits SET failures=0,open_until=0,probe_until=0 WHERE provider=?')->execute([$provider]);}
    public static function failure(string $provider): void {
        Database::transaction(function($pdo)use($provider){
            $q=$pdo->prepare('SELECT failures FROM les_circuits WHERE provider=?'.Database::forUpdate());$q->execute([$provider]);$failures=(int)$q->fetchColumn()+1;
            $pdo->prepare('UPDATE les_circuits SET failures=?,open_until=?,probe_until=0 WHERE provider=?')->execute([$failures,$failures>=5?time()+30:0,$provider]);
        });
    }
}
