<?php


function databaseConnect(): mysqli
{
    static $instance = null;

    if ($instance) {
        return $instance;
    }

    $config = require "../config/db_conf.php";

    $instance = mysqli_connect(
        $config['host'],
        $config['user'],
        $config['password'],
        $config['database'],
        $config['port']
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

function databaseErrors(): array
{
    return mysqli_error_list(databaseConnect());
}