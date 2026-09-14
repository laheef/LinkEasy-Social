<?php
namespace App;
use PDO;

/** One connection per request. Explicit additive migrations for existing installs. */
final class Database
{
    private static ?PDO $pdo = null;
    public static function pdo(): PDO {
        if (self::$pdo) return self::$pdo;
        $dsn = (string) getenv('DATABASE_DSN') ?: 'sqlite:' . LES_BASE_PATH . '/storage/linkeasy.sqlite';
        if (!str_starts_with($dsn, 'sqlite:') && !str_starts_with($dsn, 'mysql:')) throw new \RuntimeException('Unsupported database driver.');
        $pdo = new PDO($dsn, getenv('DATABASE_USER') ?: null, getenv('DATABASE_PASS') ?: null, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false]);
        self::$pdo = $pdo;
        if (self::driver() === 'sqlite') {
            $pdo->exec('PRAGMA foreign_keys=ON');
            $pdo->exec('PRAGMA busy_timeout=5000');
            // Bootstrap only a genuinely empty SQLite database; upgrades are CLI-only.
            if (!(int)$pdo->query("SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%'")->fetchColumn()) {
                Migrations::run($pdo);
            }
        }
        return $pdo;
    }
    public static function driver(): string { return (string) self::$pdo->getAttribute(PDO::ATTR_DRIVER_NAME); }
    public static function forUpdate(): string { return self::driver() === 'mysql' ? ' FOR UPDATE' : ''; }
    private static bool $transactionActive=false;
    /** Callback must perform database work only: rollback-safe retries on deadlock/busy. */
    public static function transaction(callable $work): mixed {
        $pdo=self::pdo();
        if(self::$transactionActive || $pdo->inTransaction())throw new \LogicException('Nested transactions are not supported.');
        $sqlite=self::driver()==='sqlite';
        for($attempt=0;$attempt<4;$attempt++){
            $begun=false;
            try{
                if($sqlite)$pdo->exec('BEGIN IMMEDIATE');else $pdo->beginTransaction();
                $begun=true;self::$transactionActive=true;
                $result=$work($pdo);
                if($sqlite)$pdo->exec('COMMIT');else $pdo->commit();
                return $result;
            }catch(\Throwable $e){
                try{if($begun){if($sqlite)$pdo->exec('ROLLBACK');elseif($pdo->inTransaction())$pdo->rollBack();}}catch(\Throwable){}
                $retry=$e instanceof \PDOException && (in_array((int)($e->errorInfo[1]??0),[5,6,1205,1213],true)||($e->errorInfo[0]??'')==='40001');
                if(!$retry||$attempt===3)throw $e;
                usleep(random_int(20000,60000)*($attempt+1));
            }finally{self::$transactionActive=false;}
        }
        throw new \RuntimeException('Transaction did not complete.');
    }
    public static function duplicate(\PDOException $e): bool {
        return ($e->errorInfo[0] ?? '') === '23000' && in_array((int)($e->errorInfo[1] ?? 0), [19,1062], true);
    }
}
