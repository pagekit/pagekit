<?php

namespace Pagekit\Debug\Storage;

use DebugBar\Storage\PdoStorage;

class SqliteStorage extends PdoStorage
{
    /**
     * Constructor.
     *
     * @param string $dsn
     * @param string $tableName
     * @param array  $sqlQueries
     */
    public function __construct($dsn, $tableName = 'phpdebugbar', array $sqlQueries = [])
    {
        if (class_exists('PDO') && in_array('sqlite', \PDO::getAvailableDrivers(), true)) {
            $pdo = new \PDO($dsn);
        } else {
            throw new \RuntimeException('No SQLite driver enabled.');
        }

        // create schema
        $pdo->exec('PRAGMA temp_store=MEMORY; PRAGMA journal_mode=MEMORY;');
        $pdo->exec("CREATE TABLE IF NOT EXISTS $tableName (id TEXT PRIMARY KEY, data TEXT, meta_utime TEXT, meta_datetime TEXT, meta_uri TEXT, meta_ip TEXT, meta_method TEXT)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_debugbar_id ON $tableName (id)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_debugbar_meta_utime ON $tableName (meta_utime)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_debugbar_meta_datetime ON $tableName (meta_datetime)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_debugbar_meta_uri ON $tableName (meta_uri)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_debugbar_meta_ip ON $tableName (meta_ip)");
        $pdo->exec("CREATE INDEX IF NOT EXISTS idx_debugbar_meta_method ON $tableName (meta_method)");

        parent::__construct($pdo, $tableName, $sqlQueries);
    }
}
