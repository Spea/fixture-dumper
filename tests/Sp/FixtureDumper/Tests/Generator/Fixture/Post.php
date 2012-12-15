<?php

namespace Sp\FixtureDumper\Tests\Generator\Fixture;


/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class Post
{
    private $id;

    private $title;

    private $description;

    private $author;

    private $created;

    public function setAuthor(Author $author)
    {
        $this->author = $author;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setCreated($created)
    {
        $this->created = $created;
    }

    public function getCreated()
    {
        return $this->created;
    }
}
