<?php

namespace app\Model;

class Magazine
{
    public string $name;
    public string $description;
    public string $image;
    public int $author_id;
    public string $created_at;

    public function __construct(string $name,string $description,string $image,int $author_id,string $created_at)
    {
        $this->name = $name;
        $this->description = $description;
        $this->image = $image;
        $this->author_id = $author_id;
        $this->created_at = $created_at;
    }
}