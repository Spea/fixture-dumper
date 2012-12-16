<?php

namespace Sp\FixtureDumper\Tests\Generator\Fixture;


/**
 * @Entity
 *
 * @author Martin Parsiegla <martin.parsiegla@gmail.com>
 */
class Author
{
    /**
     * @Id @Column(type="integer")
     * @GeneratedValue
     */
    private $id;

    /**
     * @Column(type="string", length=50, nullable=true)
     */
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
