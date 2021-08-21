<?php

namespace app\Controllers;

use app\Model\Author;
use app\ModelRepo\AuthorRepo;
use core\Response\ErrorResponse;
use core\Response\InfoResponse;

class AuthorController
{
    public function index(array $data) {

        if (!empty($data['page']) && !isset($data['perPage'])) {
            $page = (int)$data['page'];
            $response = AuthorRepo::index($page);
            return new InfoResponse($response);
        }
        if (!empty($data['page']) && !empty($data['perPage'])) {
            $page = (int)$data['page'];
            $perPage = (int)$data['perPage'];
            $response = AuthorRepo::index($page,$perPage);
            return new InfoResponse($response);
        }

        if (!databaseErrors()) {
            $response = AuthorRepo::index();
            return new InfoResponse($response);
        }
        return new ErrorResponse(500,"database index error");
    }

    public function add() {
        $jsonArr = json_decode(file_get_contents('php://input'), true);

        if ($jsonArr === null) {
            return new ErrorResponse(500, "Invalid input JSON");
        }

        if (!$this->validateAddAuthor($jsonArr)) {
            return new ErrorResponse(400, "JSON is not validate");
        }

        $author = new Author($jsonArr['last_name'],$jsonArr['first_name'],$jsonArr['middle_name'] ?? '');

        $response = AuthorRepo::add($author);

        return new InfoResponse($response);
    }

    public function update() {
        $jsonArr = json_decode(file_get_contents('php://input'), true);

        if ($jsonArr === null) {
            return new ErrorResponse(500, "Invalid input JSON");
        }

        if (!$this->validateUpdateAuthor($jsonArr)) {
            return new ErrorResponse(400, "JSON is not validate");
        }

        $values = [];
        $fields = [];
        foreach ($jsonArr as $key =>$val) {
            if ($key === 'id') {
                $id = (int)$val;
                continue;
            }
            $fields[] = $key . " = ? ";
            $values[] = $val;
        }
        $fields = implode(',',$fields);

        $response = AuthorRepo::update($fields,$values,$id);

        return new InfoResponse($response);
    }

    public function delete() {
        $jsonArr = json_decode(file_get_contents('php://input'), true);

        if ($jsonArr === null) {
            return new ErrorResponse(500, "Invalid input JSON");
        }
        if (empty($jsonArr['id'])) {
            return new ErrorResponse(400, "Invalid input JSON");
        }
        $id = (int)$jsonArr['id'];

        $response = AuthorRepo::delete($id);

        return new InfoResponse($response);
    }

    private function validateAddAuthor(array $jsonArr) {
        $firstCon = false;
        if ((isset($jsonArr['last_name']) && isset($jsonArr['first_name'])) && !empty($jsonArr['first_name'])) {
            $firstCon = true;
        }
        $secondCon = preg_match("/(\w{3,20})/", $jsonArr['last_name']) ? true : false;

        if ($firstCon && $secondCon) {
            return true;
        }
        return false;
    }

    private function validateUpdateAuthor(array $jsonArr) {
        if (empty($jsonArr['id'])) {
            return false;
        }
        if (isset($jsonArr['first_name']) && is_null($jsonArr['first_name'])) {
            return false;
        }
        if (isset($jsonArr['last_name']) && preg_match("/(\w{3,20})/", $jsonArr['last_name']) === false) {
            return false;
        }
        if (!isset($jsonArr['last_name']) && !isset($jsonArr['first_name'])) {
            return false;
        }
        return true;
    }
}