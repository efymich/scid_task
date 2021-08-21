<?php

function dataBaseConnect (): mysqli {

    static $instance = null;

    if ($instance) {
        return $instance;
    }

    $config = require 'migration_dbconf.php';

    $instance = mysqli_connect(
        $config['host'],
        $config['user'],
        $config['password'],
        $config['database'],
        $config['port'],
    );

    return $instance;
}

function databaseClose(): bool
{
    return mysqli_close(databaseConnect());
}


function databaseExecute(string $query, ...$args): ?mysqli_result
{
    $stmt = mysqli_prepare(databaseConnect(), $query);
    if (!$stmt) {
        return null;
    }
    $type = '';
    foreach ($args as $arg) {
        switch (gettype($arg)) {
            case 'integer':
                $type .= 'i';
                break;
            case 'double':
                $type .= 'd';
                break;
            default:
                $type .= 's';
                break;
        }

    }

    if ($type) {
        mysqli_stmt_bind_param($stmt, $type, ...$args);
    }

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    return $result ?: null;
}

function databaseErrors(): array {
    return mysqli_error_list(databaseConnect());
}

function migrationInit () {

    $query = 'CREATE TABLE IF NOT EXISTS migration_history (id serial PRIMARY KEY , name varchar(256) NOT NULL UNIQUE, created_at timestamp DEFAULT now() )';

    databaseExecute($query);

}

function getMigrationNameHistory () : array {
    $query = "SELECT name FROM migration_history ORDER BY id DESC ";

    $result = databaseExecute($query);

    $table = mysqli_fetch_assoc($result);

    if (!$table) {
        return [];
    }

    return $table;
}

function saveMigration (string $name) {
    $query = "INSERT INTO migration_history (name) VALUES (?)";

    databaseExecute($query,$name);
}

function deleteMigrationByName (string $name) {
    $query = "DELETE FROM migration_history where name = ?";

    databaseExecute($query,$name);


}