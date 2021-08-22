    <?php
    
    $pull = "ALTER TABLE magazines ADD CONSTRAINT FOREIGN KEY author_id_fk (author_id) REFERENCES authors(id) ON DELETE CASCADE ";
    
    $rollback = "ALTER TABLE magazines DROP FOREIGN KEY author_id_fk";
    
    return [
        'pull' => $pull,
        'rollback' => $rollback
        ];