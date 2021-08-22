<?php

namespace app\Controllers;

use app\Model\Magazine;
use app\ModelRepo\MagazineRepo;
use core\Response\ErrorResponse;
use core\Response\InfoResponse;

class MagazineController
{
    public function index(array $data) {
        $page = (int)$data['page'] ?? null;
        $perPage = (int)$data['perPage'] ?? null;
        $author_id = (int)$data['author_id'] ?? null;

        if (!databaseErrors()) {
            $response = MagazineRepo::index($author_id,$page,$perPage);
            return new InfoResponse($response);
        }
        return new ErrorResponse(500,"database index error");
    }

    public function add() {
        $arr = $_REQUEST;

        if ($arr === null) {
            return new ErrorResponse(400, "Invalid input JSON");
        }

        if (!$this->validateAddMagazine($arr)) {
            return new ErrorResponse(400, "JSON is not validate");
        }

        if (empty($this->UploadImage())){
            $image = '';
        } else {
            $image = "images/" . $this->UploadImage();
        }

        $magazine = new Magazine($arr['name'],$arr['description'] ?? '',$image,$arr['author_id'],$arr['created_at']);

        $response = MagazineRepo::add($magazine);

        return new InfoResponse($response);
    }

    public function update(){
        $arr = $_REQUEST;

        if ($arr === null) {
            return new ErrorResponse(400, "Invalid input JSON");
        }

        if (!$this->validateUpdateMagazine($arr)) {
            return new ErrorResponse(400, "JSON is not validate");
        }

        $values = [];
        $fields = [];
        foreach ($arr as $key =>$val) {
            if ($key === 'id') {
                $id = (int)$val;
                continue;
            }
            $fields[] = $key . " = ? ";
            $values[] = $val;
        }
        $fields = implode(',',$fields);

        $response = MagazineRepo::update($fields,$values,$id);

        return new InfoResponse($response);
    }

    public function delete(){
        $arr = $_REQUEST;

        if ($arr === null) {
            return new ErrorResponse(400, "Invalid input JSON");
        }
        if (empty($arr['id'])) {
            return new ErrorResponse(400, "Invalid input JSON");
        }
        $id = (int)$arr['id'];

        $response = MagazineRepo::delete($id);

        return new InfoResponse($response);
    }

    private function UploadImage(): ?string
    {
        if ((is_uploaded_file($_FILES['image']['tmp_name'])) &&
            ($_FILES['image']['type'] === 'image/jpeg' || $_FILES['image']['type'] === 'image/png') &&
            ($_FILES['image']['size'] <= 2000000)
            ) {
            $imageSum = md5_file($_FILES['image']['tmp_name']);
            if (!file_exists("images/" . $imageSum)) {
                move_uploaded_file($_FILES['image']['tmp_name'], "images/" . $imageSum);
            }
            return $imageSum;
        }
        return null;
    }

    private function validateAddMagazine(array $arr) {
        $firstCon = false;
        if ((isset($arr['name']) && isset($arr['author_id']))) {
            $firstCon = true;
        }
        $secondCon = preg_match("/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $arr['created_at']) ? true : false;

        if ($firstCon && $secondCon) {
            return true;
        }
        return false;
    }

    private function validateUpdateMagazine(array $arr) {
        if (empty($arr['id'])) {
            return false;
        }
        if (isset($arr['last_name']) && !preg_match("/^\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $arr['created_at'])) {
            return false;
        }
        return true;
    }
}