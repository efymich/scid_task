<?php


namespace app\Controllers;


use core\Response\ErrorResponse;
use core\Response\InfoResponse;
use core\Response\Response;
use core\Response\Response_interface\iResponse;
use Hashids\Hashids;
use mysqli_sql_exception;

class BasicController
{
    public function addUrl(array $data): Response
    {
        $url = json_decode(file_get_contents('php://input'), true);

        if ($url === null) {
            return new ErrorResponse(500, "Invalid input JSON");
        }

        if (!$this->validateUrl($url)) {
            return new ErrorResponse(400, "Url is not validate");
        }

        $mysqli = dataBaseConnect();

        if (isset($url['customToken'])) {
            $token = $url['customToken'];
            if (strlen($token) > 10) {
                return new ErrorResponse(500, "token have more than 10 symbols!");
            }
            $stmt = mysqli_prepare($mysqli, "SELECT token FROM urlTable");
            mysqli_stmt_bind_param($stmt, 's', $token);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            while ($row = mysqli_fetch_assoc($result)) {
                if ($token === $row['token']) {
                    return new ErrorResponse(500, "token must be unique!");
                }
            }
        }

        mysqli_begin_transaction($mysqli);

        try {
            $longUrl = $url['href'];
            $stmt = mysqli_prepare($mysqli, "INSERT INTO urlTable (longUrl) VALUES (?) ");
            mysqli_stmt_bind_param($stmt, 's', $longUrl);
            mysqli_stmt_execute($stmt);

            // operator RETURNING is not supported in my local mariadb version
            $stmt = mysqli_prepare($mysqli, "SELECT id FROM urlTable ORDER BY created_at DESC LIMIT 1");
            mysqli_stmt_bind_param($stmt, 'i', $longUrl);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $resArr = mysqli_fetch_assoc($result);

            $id = $resArr['id'];
            $token = $this->encodeToken($id);
            if (isset($url['customToken'])) {
                $token = $url['customToken'];
            }

            $stmt = mysqli_prepare($mysqli, "UPDATE urlTable SET token = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'si', $token, $id);
            mysqli_stmt_execute($stmt);

            mysqli_commit($mysqli);
        } catch (mysqli_sql_exception $exception) {
            mysqli_rollback($mysqli);
            throw $exception;
        }

        if (!databaseErrors()) {
            $res = [
                "message" => "Query is OK",
                "newUrl" => "$token"
            ];
            return new InfoResponse($res);
        }
        return new ErrorResponse(500, "Database error!");
    }

    public function index(array $data): iResponse
    {
        $token = $data['token'];

        $query = "SELECT longUrl FROM urlTable WHERE token = ?";

        $result = databaseExecute($query, $token);

        $resArray = mysqli_fetch_assoc($result);

        if (!empty($longUrl = $resArray['longUrl'])) {
            return new InfoResponse("redirect", ["Location" => $longUrl]);
        }

        return new ErrorResponse(500, "Unexpected url!");
    }

    private function encodeToken(int $id)
    {
        $hashids = new Hashids('profi_task', 10);
        $res = $hashids->encode($id);
        return $res;
    }

    private function validateUrl(array $urlRaw): bool
    {
        $url = parse_url($urlRaw['href']);
        if ($url['scheme'] === null || $url['host'] === null) {
            return false;
        }

        $firstCon = preg_match("/(http|https|ftp)/", $url['scheme']) ? true : false;
        $secondCon = preg_match("/^(www.|)[a-zA-Z0-9]+.(com|ru|org|net|gov|biz)$/", $url['host']) ? true : false;

        if ($firstCon && $secondCon) {
            return true;
        }
        return false;
    }
}