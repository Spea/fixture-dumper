<?php

namespace Sp\FixtureDumper\Tests\Generator\Fixture;


/**
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class Author
{
    private $id;

    private $username;

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }


}
