<?php

namespace App\models;

final class User
{
    private $id;
    private $fname;
    private $lname;
    private $email;
    private $password;
    private $verified;
    private $verified_at;
    private $created_at;

    public function __construct($id, $fname, $lname, $email, $password, $verified, $verified_at, $created_at)
    {
        $this->id          = $id;
        $this->fname       = $fname;
        $this->lname       = $lname;
        $this->email       = $email;
        $this->password    = $password;
        $this->verified    = $verified;
        $this->verified_at = $verified_at;
        $this->created_at  = $created_at;
    }
    // setter methods
    public function setId($id): void
    {
        $this->id = $id;
    }

    public function setFname($fname): void
    {
        $this->fname = $fname;
    }

    public function setLname($lname): void
    {
        $this->lname = $lname;
    }

    public function setEmail($email): void
    {
        $this->email = $email;
    }

    public function setPassword($password): void
    {
        $this->password = $password;
    }

    public function setVerified($verified): void
    {
        $this->verified = $verified;
    }

    public function setVerifiedAt($verified_at): void
    {
        $this->verified_at = $verified_at;
    }

    public function setCreatedAt($created_at): void
    {
        $this->created_at = $created_at;
    }

    // getter methods
    public function getId()
    {
        return $this->id;
    }

    public function getFname()
    {
        return $this->fname;
    }

    public function getLname()
    {
        return $this->lname;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getVerified()
    {
        return $this->verified;
    }

    public function getVerifiedAt()
    {
        return $this->verified_at;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function toArray()
    {
        $data = get_object_vars($this);
        return array_filter($data, function ($item) {
            return !empty($item);
        });
    }
}
