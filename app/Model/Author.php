<?php

namespace app\Model;

class Author
{
    public string $last_name;
    public string $first_name;
    public string $middle_name;

    public function __construct(string $last_name,string $first_name, string $middle_name)
    {
        $this->last_name = $last_name;
        $this->first_name = $first_name;
        $this->middle_name = $middle_name;
    }
}