<?php

namespace core\Response;

use core\Response\Response_interface\iResponse;

abstract class Response implements iResponse
{
    public int $responseCode;
    public string $wrapper = 'data';
    public $data;
    private array $headers = [];

    public function giveResponse()
    {
        $this->setHeaders();

        die($this->convertArray());
    }

    protected function setHeaders()
    {
        foreach ($this->headers as $headerKey => $headerVal) {
            header("$headerKey:$headerVal;");
        }
    }
}