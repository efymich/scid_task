<?php

namespace app\ModelRepo;

use app\Model\Author;
use core\Response\ErrorResponse;

class AuthorRepo
{
    private static string $tableName = 'authors';

    public static function index(int $page = null, int $perPage = null)
    {
        $sql = "SELECT * FROM " . self::$tableName;


        if (!is_null($page) && is_null($perPage)) {
            $sql .= " LIMIT " . ($page - 1) . " ,1";
        }
        if (!is_null($page) && !is_null($perPage)) {
            $offset =($page*$perPage)-$perPage;
            $sql .= " LIMIT $offset, $perPage";
        }
        $result = databaseExecute($sql);

        $authors = [];
        while ($row = $result->fetch_assoc()) {
            $authors[] = new Author($row['last_name'],$row['first_name'],$row['middle_name']);
        }
        return $authors;
    }

    public static function add(Author $author)
    {
        $sql = "INSERT INTO " . self::$tableName . " (last_name,first_name,middle_name) VALUES (?,?,?)";

        databaseExecute($sql,$author->last_name,$author->first_name,$author->middle_name);
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