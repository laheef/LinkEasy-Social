<?php
namespace App;
/** Fixed-window, atomic database limiter. Shared MySQL supports multiple web nodes. */
final class RateLimiter
{
    private static function key(string $bucket,?string $identity): string { return hash('sha256',$bucket.'|'.($identity??Request::clientIp())); }
    public static function check(string $bucket,int $maxAttempts=5,int $windowSeconds=900,?string $identity=null): bool {
        $key=self::key($bucket,$identity);$now=time();
        return Database::transaction(function($pdo)use($key,$now,$maxAttempts,$windowSeconds){
            $sql=Database::driver()==='mysql'?"INSERT INTO les_rate_limits(bucket,hits,reset_at) VALUES (?,0,?) ON DUPLICATE KEY UPDATE bucket=VALUES(bucket)":"INSERT OR IGNORE INTO les_rate_limits(bucket,hits,reset_at) VALUES (?,0,?)";
            $pdo->prepare($sql)->execute([$key,$now+$windowSeconds]);
            $stmt=$pdo->prepare('SELECT hits,reset_at FROM les_rate_limits WHERE bucket=?'.Database::forUpdate());$stmt->execute([$key]);$row=$stmt->fetch();
            if((int)$row['reset_at']<=$now) {$row=['hits'=>0,'reset_at'=>$now+$windowSeconds];}
            if((int)$row['hits']>=$maxAttempts)return false;
            $pdo->prepare('UPDATE les_rate_limits SET hits=?,reset_at=? WHERE bucket=?')->execute([(int)$row['hits']+1,$row['reset_at'],$key]);return true;
        });
    }
    public static function retryAfter(string $bucket,int $windowSeconds=900,?string $identity=null): int {
        $stmt=Database::pdo()->prepare('SELECT reset_at FROM les_rate_limits WHERE bucket=?');$stmt->execute([self::key($bucket,$identity)]);
        return max(1,(int)$stmt->fetchColumn()-time());
    }
    public static function reject(int $seconds=60): never {
        header('Retry-After: '.max(1,$seconds));Request::reject(429,'Too many requests. Please wait before trying again.');
    }
}
