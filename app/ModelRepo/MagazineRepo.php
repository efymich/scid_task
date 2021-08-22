<?php

namespace app\ModelRepo;


use app\Model\Author;
use app\Model\Magazine;
use core\Response\ErrorResponse;

class MagazineRepo
{
    private static string $tableName = 'magazines';

    public static function index(int $author_id = null,int $page = null, int $perPage = null)
    {
        $sql = "SELECT name,description,image,author_id,created_at FROM " . self::$tableName;

        if (!is_null($author_id)) {
            $sql .= " WHERE author_id = ?";

            if (!is_null($page) && is_null($perPage)) {
                $sql .= " LIMIT " . ($page - 1) . ",1 ";
            }
            if (!is_null($page) && !is_null($perPage)) {
                $offset =($page*$perPage)-$perPage;
                $sql .= " LIMIT $offset, $perPage ";
            }

            $result = databaseExecute($sql,$author_id);

            $magazines = [];
            while ($row = $result->fetch_assoc()) {
                $magazines[] = new Magazine($row['name'],$row['description'],$row['image'],$row['author_id'],$row['created_at']);
            }
            return $magazines;
        }

        $result = databaseExecute($sql);

        $magazines = [];
        while ($row = $result->fetch_assoc()) {
            $magazines[] = new Magazine($row['name'],$row['description'],$row['image'],$row['author_id'],$row['created_at']);
        }
        return $magazines;
    }

    public static function add(Magazine $magazine)
    {
        $sql = "INSERT INTO " . self::$tableName . " (name,description,image,author_id,created_at) VALUES (?,?,?,?,?)";

        databaseExecute($sql,$magazine->name,$magazine->description,$magazine->image,$magazine->author_id,$magazine->created_at);
        return ['message' => "add is complete!"];
    }

    public static function update(string $fields, array $values, int $id)
    {
        $sql = "UPDATE " . self::$tableName . " SET " . $fields . "WHERE id = $id";

        databaseExecute($sql,...$values);

        if (databaseErrors()) {
            return new ErrorResponse(500,"Incorrect query");
        }
        return ['message' => "update is complete!"];
    }

    public static function delete(int $id)
    {
        $sql = "DELETE FROM " .  self::$tableName . " WHERE id = ?";

        databaseExecute($sql,$id);
        if (databaseErrors()) {
            return new ErrorResponse(500,"Incorrect query");
        }
        return ['message' => "delete is complete!"];
    }
}