<?php

namespace core\Response;


class ErrorResponse extends Response
{
    public int $responseCode;
    public string $description;
    private array $headers = [
        "Content-Type" => "application/json"
    ];


    public function __construct($responseCode, $description, $headers = [])
    {
        $this->responseCode = $responseCode;
        foreach ($headers as $headerKey => $headerVal) {
            $this->headers[$headerKey] = $headerVal;
        }
        $this->description = $description;
    }

    public function convertArray()
    {
        $arr = [
            'code' => $this->responseCode,
            'description' => $this->description
        ];

        return json_encode($arr);
    }

    public function setHeaders()
    {
        foreach ($this->headers as $headerKey => $headerVal) {
            header("$headerKey:$headerVal");
        }
    }

    public function giveResponse()
    {
        $this->setHeaders();

        die($this->convertArray());
    }
}