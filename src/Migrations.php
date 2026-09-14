<?php
namespace App;
use PDO;
/** Versioned, checksum-verified upgrades. Back up before running; MySQL DDL is not transactional. */
final class Migrations
{
    public static function run(PDO $pdo): array {
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        $mysql = $driver === 'mysql';
        if ($mysql) {
            if ((int)$pdo->query("SELECT GET_LOCK('les_schema_migrations', 10)")->fetchColumn() !== 1) throw new \RuntimeException('Migration lock unavailable.');
        } else $pdo->exec('BEGIN IMMEDIATE');
        try {
            // Baseline is idempotent; never drop or recreate existing application data.
            self::execute($pdo, file_get_contents(LES_BASE_PATH . '/database/schema.' . $driver . '.sql'));
            $pdo->exec('CREATE TABLE IF NOT EXISTS les_migrations (version VARCHAR(100) PRIMARY KEY, checksum VARCHAR(64) NOT NULL, applied_at VARCHAR(30) NOT NULL)');
            $applied = [];
            foreach (glob(LES_BASE_PATH . '/database/migrations/*.' . $driver . '.sql') as $file) {
                $version = basename($file); $hash = hash_file('sha256', $file);
                $stmt=$pdo->prepare('SELECT checksum FROM les_migrations WHERE version=?');$stmt->execute([$version]);$old=$stmt->fetchColumn();
                if ($old !== false) { if (!hash_equals($old,$hash)) throw new \RuntimeException('Applied migration checksum mismatch.'); continue; }
                self::execute($pdo, file_get_contents($file));
                $pdo->prepare('INSERT INTO les_migrations(version,checksum,applied_at) VALUES (?,?,?)')->execute([$version,$hash,gmdate('c')]);
                $applied[]=$version;
            }
            if (!$mysql) $pdo->exec('COMMIT');
            return $applied;
        } catch (\Throwable $e) {
            if (!$mysql) $pdo->exec('ROLLBACK');
            throw $e;
        } finally { if ($mysql) $pdo->query("SELECT RELEASE_LOCK('les_schema_migrations')"); }
    }
    private static function execute(PDO $pdo, string $sql): void {
        $sql=preg_replace('/^\s*--.*$/m','',$sql);
        foreach (explode(';',$sql) as $statement) if (trim($statement)!=='') $pdo->exec($statement);
    }
}
