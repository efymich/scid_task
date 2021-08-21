<?php

namespace app\ModelRepo;


use app\Model\Author;
use app\Model\Magazine;

class MagazineRepo
{
    private static string $tableName = 'magazines';

    public static function index(int $page = null, int $perPage = null, int $author_id = null)
    {
        $sql = "SELECT name,description,image,author_id,created_at FROM " . self::$tableName;

        if (!is_null($page)) {
            $sql .= " OFFSET $page LIMIT 1";
        }
        if (!is_null($page) && !is_null($perPage)) {
            $sql .= " OFFSET $page LIMIT $perPage";
        }
        if (!is_null($author_id)) {
            $sql .= " WHERE author_id = ?";
            $result = databaseExecute($sql,$author_id);

            $magazines = [];
            while ($row = $result->fetch_assoc()) {
                $magazines = new Magazine($row['name'],$row['description'],$row['image'],$row['created_at']);
            }
            return $magazines;

        }

        $result = databaseExecute($sql);

        $magazines = [];
        while ($row = $result->fetch_assoc()) {
            $magazines = new Magazine($row['name'],$row['description'],$row['image'],$row['author_id'],$row['created_at']);
        }
        return $magazines;
    }


    public static function add($author)
    {

        // TODO: Implement add() method.
    }

    public static function update($author)
    {

        // TODO: Implement update() method.
    }

    public static function delete($author)
    {

        // TODO: Implement delete() method.
    }
}