    <?php
    
    $pull = "CREATE TABLE authors (
        id serial PRIMARY KEY ,
        last_name varchar (20),
        first_name varchar (20) NOT NULL,
        middle_name varchar (20)
        )";
    
    $rollback = "DROP TABLE authors";
    
    return [
        'pull' => $pull,
        'rollback' => $rollback
        ];