#!/usr/bin/env php
<?php

require 'migration_dbconnect.php';

$config = require 'migration_dbconf.php';

if(empty($argv[1])) {
    echo "Input command! Type Create , Pull, Rollback, Wipe, Rollback n\n";
    var_dump($argv);
    return;
}

switch (strtolower($argv[1])) {
    case "create":
        create($argv,$config);
        break;
    case "pull":
        pull($config);
        break;
    case "rollback":
        rollback($config,$argv);
        break;
    case "wipe":
        wipe($config);
        break;
    default:
        echo "Unknown command!\n";
        return;
}

function create(array $argv,array $config) {
    if (empty($argv[2])) {
        echo "Print correct migration name!\n" ;
    }

    $name = $argv[2];
    $name = time() . '_' . str_replace(' ','_',$name);

    $file = fopen($config['dir'] . $name . '.php','wb+');
    $content = <<<'CONTENT'
    <?php
    
    $pull = "";
    
    $rollback = "";
    
    return [
        'pull' => $pull,
        'rollback' => $rollback
        ];
CONTENT;

    if (fwrite($file,$content)){
        echo "Migration created!\n";
    } else {
        echo "Migration fault!\n";
    }
    fclose($file);
}

function pull(array $config) {
    migrationInit();
    $migrationNameHistory = getMigrationNameHistory();

    $migrations = glob($config['dir'] . '*.php');

    if (!$migrations) {
        echo "Migration fault!";
        return;
    }

    $migrations = sortMigrations($migrations);

    foreach ($migrations as $migration) {
        $name = basename($migration);
        if (in_array($name,$migrationNameHistory,true)) {
            continue;
        }

        $migrationData = require $migration;

        if (empty($migrationData['pull'])) {
            continue;
        }

        databaseExecute($migrationData['pull']);

        if (databaseErrors()) {
            die("Migration pull fail " . databaseErrors());
        }

        saveMigration($name);

        if (!empty($migrationData['rollback'] && databaseErrors())) {
            databaseExecute($migrationData['rollback']);
            continue;
        }

        echo "\n" . $name . " - migrate!";
    }

    echo "Finished!\n";
}
function rollback($config, array $argv) {

    migrationInit();
    $counter = null;

    if (!empty($argv[2] && (int)$argv[2] > 0)) {
        $counter = (int)$argv[2];
    }

    $migrationNameHistory = getMigrationNameHistory();

    foreach ($migrationNameHistory as $name) {
        if (!file_exists($config['dir'] . $name)){
            die("Migration not found!");
        }

        if ($counter === 0) {
            break;
        }


        $migration = require $config['dir'] . $name;

        if (!empty($migration['rollback'])) {
            databaseExecute($migration['rollback']);
        }
        deleteMigrationByName($name);

        if (databaseErrors()) {
            die("Migration rollback fail " . databaseErrors());
        }

        if ($counter !== null) {
            $counter--;
        }

        echo "\n" . $name . " - rollback!";
    }
    echo "\nFinished!\n";
}

function wipe($config) {
    $databaseName = $config['database'];
    $query = "SELECT CONCAT('DROP TABLE IF EXISTS ', TABLE_SCHEMA, '.', TABLE_NAME, ';')
FROM information_schema.TABLES
WHERE TABLE_SCHEMA = ?";

    $dropRequestprepare = databaseExecute($query,$databaseName);

    while ($row = mysqli_fetch_row($dropRequestprepare)) {
        $dropRequest = $row[0];
        databaseExecute($dropRequest);
    }

//    pull($config);
    echo "Wipe complete!";
}

function sortMigrations(array $data): array {
    $n = count($data);

    for ($i=1 ; $i < $n;$i++) {
        $j = $i;
        while ($j > 0) {
            $current = explode('_',$data[$j])[0] ?? null;
            $prev = explode('_',$data[$j-1])[0] ?? null;

            if ($current === null) {
                die("Invalid file - " . $current);
            }
            if ($prev === null) {
                die("Invalid file - " . $prev);
            }

            if ((int)$current < (int)$prev) {
                $tmp = $data[$j];
                $data[$j] = $data[$j-1];
                $data[$j-1] = $tmp;
            }

            $j--;
        }
    }
    return $data;
}