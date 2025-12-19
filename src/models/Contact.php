<?php

namespace App\models;

final class Contact
{
    private $id;
    private $firstname;
    private $middlename;
    private $lastname;
    private $email;
    private $primary_mobile;
    private $alternate_mobile;
    private $avatar;
    private $created_at;
    private $userid;
    public function __construct($id, $firstname, $middlename, $lastname, $email, $primary_mobile, $alternate_mobile, $avatar, $created_at, $userid)
    {
        $this->id               = $id;
        $this->firstname        = $firstname;
        $this->middlename       = $middlename;
        $this->lastname         = $lastname;
        $this->email            = $email;
        $this->primary_mobile   = $primary_mobile;
        $this->alternate_mobile = $alternate_mobile;
        $this->avatar           = $avatar;
        $this->created_at       = $created_at;
        $this->userid           = $userid;
    }

    //setter methods
    public function setId($id): void
    {
        $this->id = $id;
    }

    public function setFirstname($firstname): void
    {
        $this->firstname = $firstname;
    }

    public function setMiddlename($middlename): void
    {
        $this->middlename = $middlename;
    }

    public function setLastname($lastname): void
    {
        $this->lastname = $lastname;
    }

    public function setEmail($email): void
    {
        $this->email = $email;
    }

    public function setPrimaryMobile($primary_mobile): void
    {
        $this->primary_mobile = $primary_mobile;
    }

    public function setAlternateMobile($alternate_mobile): void
    {
        $this->alternate_mobile = $alternate_mobile;
    }

    public function setAvatar($avatar): void
    {
        $this->avatar = $avatar;
    }

    public function setCreatedAt($created_at): void
    {
        $this->created_at = $created_at;
    }

    public function setUserid($userid): void
    {
        $this->userid = $userid;
    }

    //getter methods
    public function getId()
    {
        return $this->id;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function getMiddlename()
    {
        return $this->middlename;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPrimaryMobile()
    {
        return $this->primary_mobile;
    }

    public function getAlternateMobile()
    {
        return $this->alternate_mobile;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getUserid()
    {
        return $this->userid;
    }

    public function toArray()
    {
        $data = get_object_vars($this);
        return array_filter($data, function ($item) {
            return !empty($item);
        });
    }
}
