<?php

namespace App\controllers;

use App\util\UtilityFunctions;

final class SiteController extends BaseController
{
    public function __construct()
    {
        // get database connection from parent class
        parent::__construct();
    }
    public function home()
    {
        return ['templatePage' => 'pages/home.html.twig', 'pageData' => []];
    }
    public function about()
    {
        return ['templatePage' => 'pages/about.html.twig','pageData' => []];
    }
    public function signup()
    {
        return ['templatePage' => 'pages/register.html.twig','pageData' => []];
    }
    public function login()
    {
        return ['templatePage' => 'pages/login.html.twig','pageData' => []];
    }
    public function forgot()
    {
        return ['templatePage' => 'pages/forgot.html.twig','pageData' => []];
    }
    public function myaccount()
    {
        // fetch session user data from database
        $user   = UtilityFunctions::getSessionUser();
        $result = $this->dbcon->read('myc_users', "id = '" . $user['userid'] . "'");
        $data   = [];
        if ($result->num_rows) {
            $data = $result->fetch_assoc();
        }
        return ['templatePage' => 'pages/myaccount.html.twig','pageData' => $data];
    }
    public function changepassword()
    {
        return ['templatePage' => 'pages/changepassword.html.twig', 'pageData' => []];
    }
    public function contact()
    {
        $contact = [];
        if (isset($_GET['id'])) {
            $id     = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
            $whereCondition = "id = '$id' AND userid = " . UtilityFunctions::getSessionUser()['userid'];
            $result = $this->dbcon->read('myc_contacts', $whereCondition);
            if ($result->num_rows) {
                $contact           = $result->fetch_assoc();
                $contact['avatar'] = base64_encode($contact['avatar']);
            }
        }
        return ['templatePage' => 'pages/contact.html.twig','pageData' => $contact];
    }
}
