    <?php
    
    $pull = "CREATE TABLE magazines (
        id serial PRIMARY KEY,
        name varchar (20),
        description text,
        image text,
        author_id bigint unsigned,
        created_at date )";
    
    $rollback = "DROP TABLE magazines";
    
    return [
        'pull' => $pull,
        'rollback' => $rollback
        ];