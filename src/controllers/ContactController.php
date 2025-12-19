<?php

namespace App\controllers;

use App\util\Response;
use App\util\UtilityFunctions;
use App\models\Contact;

// require_once removed to avoid side effects; rely on autoloader and PSR-4 autoloading
final class ContactController extends BaseController
{
    public function __construct()
    {
        // get database connection from parent class
        parent::__construct();
    }
    public function create()
    {
        $avatarFile = filter_var($_POST['avatar'], FILTER_SANITIZE_STRING);
        $fname      = filter_var($_POST['fname'], FILTER_SANITIZE_STRING);
        $mname      = filter_var($_POST['mname'], FILTER_SANITIZE_STRING);
        $lname      = filter_var($_POST['lname'], FILTER_SANITIZE_STRING);
        $email      = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
        $pmobile    = filter_var($_POST['pmobile'], FILTER_SANITIZE_STRING);
        $amobile    = filter_var($_POST['amobile'], FILTER_SANITIZE_STRING);
        $errors     = [];
        if (strlen($fname) < 2 || strlen($fname) > 20) {
            $errors['fname'] = 'First name must be at least 2 characters long and maximum 20 characters long';
        }
        if (!empty($mname) && (strlen($mname) < 2 || strlen($mname) > 25)) {
            $errors['mname'] = 'Middle name must be at least 2 characters long and maximum 25 characters long';
        }
        if (strlen($lname) < 2 || strlen($lname) > 25) {
            $errors['lname'] = 'Last name must be at least 2 characters long and maximum 25 characters long';
        }
        if (!empty($email)) {
            $emailError = UtilityFunctions::validateEmail($email);
            if (!empty($emailError)) {
                $errors = array_merge($errors, $emailError);
            }
        }
        if (!UtilityFunctions::validatePhone($pmobile)) {
            $errors['pmobile'] = 'Please provide a valid mobile number';
        }
        if (!empty($amobile) && !UtilityFunctions::validatePhone($amobile)) {
            $errors['amobile'] = 'Please provide a valid mobile number';
        }
        if (count($errors)) {
            $response = new Response(400, 'Error', $errors);
            return $response->toJson();
        }
        // validation successful
        $avatar = null;
        if (!empty($avatarFile) && file_exists($avatarFile)) {
            $avatar = file_get_contents($avatarFile);
        }
        $user      = UtilityFunctions::getSessionUser();
        $contact   = new Contact(null, $fname, $mname, $lname, $email, $pmobile, $amobile, $avatar, null, $user['userid']);
        $data      = $contact->toArray();
        $contactId = $this->dbcon->create('myc_contacts', $data);
        if (!$contactId) {
            $response = new Response(500, 'Error', ['message' => 'Error creating contact, please try again later']);
            return $response->toJson();
        }
        // delete the avatar file
        if (!empty($avatarFile) && file_exists($avatarFile)) {
            @unlink($avatarFile);
        }
        $response = new Response(200, 'Success', ['message' => 'New contact created successfully']);
        return $response->toJson();
    }
    public function read()
    {
        $draw    = (int) $_POST['draw'] ?? 1;
        $start   = (int) $_POST['start'] ?? 0;
        $length  = (int) $_POST['length'] ?? 10;
        $keyword = trim($_POST['search']['value'] ?? '');
        if ($start < 0) {
            $start = 0;
        }
        if ($length <= 0 || $length > 1000) {
            $length = 10; // default length
        }
        $data = [
            'draw'            => $draw,
            'recordsTotal'    => 0,
            'recordsFiltered' => 0,
            'list'            => [],
        ];
        $grossResult          = $this->dbcon->read('myc_contacts');
        $data['recordsTotal'] = $grossResult->num_rows ?? 0;
        $whereCondition       = 'userid = ' . UtilityFunctions::getSessionUser()['userid'];
        $keyword              = $this->dbcon->connection->real_escape_string($keyword);
        if (strlen($keyword)) {
            $keyword = (string) $keyword;
            $whereCondition .= " AND (";
            $whereCondition .= "firstname LIKE '%" . $keyword . "%' ";
            $whereCondition .= "OR middlename LIKE '%" . $keyword . "%' ";
            $whereCondition .= "OR lastname LIKE '%" . $keyword . "%' ";
            $whereCondition .= "OR email LIKE '%" . $keyword . "%'";
            $whereCondition .= "OR primary_mobile LIKE '%" . $keyword . "%'";
            $whereCondition .= "OR alternate_mobile LIKE '%" . $keyword . "%'";
            $whereCondition .= ")";
        }
        $orderBy                 = 'firstname ASC';
        $result                  = $this->dbcon->read('myc_contacts', $conditions = $whereCondition, $orderBy, $limit = "$start, $length");
        $rows                    = [];
        $data['recordsFiltered'] = (strlen($whereCondition)) ? $result->num_rows : $grossResult->num_rows;
        if ($result->num_rows) {
            while ($row = $result->fetch_assoc()) {
                if (!empty($row['avatar'])) {
                    $row['avatar'] = base64_encode($row['avatar']);
                }
                $rows[] = $row;
            }
            $data['list'] = $rows;
        }
        $response = new Response(200, 'Success', $data);
        $response->toJson();
    }
    public function update()
    {
        // get data from PUT request
        $putData = file_get_contents('php://input');
        parse_str($putData, $_PUT);
        $id = filter_var($_PUT['id'], FILTER_SANITIZE_NUMBER_INT);
        if (empty($id) || !is_numeric($id) || $id <= 0) {
            $errors['form'] = 'Contact not found';
            $response       = new Response(400, 'Error', $errors);
            return $response->toJson();
        }
        $user       = UtilityFunctions::getSessionUser();
        $userid     = $user['userid'];
        $conditions = 'id = ' . $id . ' AND userid = ' . $userid;
        $result     = $this->dbcon->read('myc_contacts', $conditions);
        if (!$result->num_rows) {
            $errors['form'] = 'Contact not found';
            $response       = new Response(400, 'Error', $errors);
            return $response->toJson();
        }
        $avatarFile = null;
        if (isset($_PUT['avatar']) && !empty($_PUT['avatar'])) {
            $avatarFile = filter_var($_PUT['avatar'], FILTER_SANITIZE_STRING);
        }
        $fname   = filter_var($_PUT['fname'], FILTER_SANITIZE_STRING);
        $mname   = filter_var($_PUT['mname'], FILTER_SANITIZE_STRING);
        $lname   = filter_var($_PUT['lname'], FILTER_SANITIZE_STRING);
        $email   = filter_var($_PUT['email'], FILTER_SANITIZE_STRING);
        $pmobile = filter_var($_PUT['pmobile'], FILTER_SANITIZE_STRING);
        $amobile = filter_var($_PUT['amobile'], FILTER_SANITIZE_STRING);
        $errors  = [];
        if (strlen($fname) < 2 || strlen($fname) > 20) {
            $errors['fname'] = 'First name must be at least 2 characters long and maximum 20 characters long';
        }
        if (!empty($mname) && (strlen($mname) < 2 || strlen($mname) > 25)) {
            $errors['mname'] = 'Middle name must be at least 2 characters long and maximum 25 characters long';
        }
        if (strlen($lname) < 2 || strlen($lname) > 25) {
            $errors['lname'] = 'Last name must be at least 2 characters long and maximum 25 characters long';
        }
        if (!empty($email)) {
            $emailError = UtilityFunctions::validateEmail($email);
            if (!empty($emailError)) {
                $errors = array_merge($errors, $emailError);
            }
        }
        if (!UtilityFunctions::validatePhone($pmobile)) {
            $errors['pmobile'] = 'Please provide a valid mobile number';
        }
        if (!empty($amobile) && !UtilityFunctions::validatePhone($amobile)) {
            $errors['amobile'] = 'Please provide a valid mobile number';
        }
        if (count($errors)) {
            $response = new Response(400, 'Error', $errors);
            return $response->toJson();
        }
        // validation successful
        $avatar = null;
        if (!empty($avatarFile) && file_exists($avatarFile)) {
            $avatar = file_get_contents($avatarFile);
        } else {
            // retain existing avatar
            $existingContact = $result->fetch_assoc();
            $avatar          = $existingContact['avatar'];
        }
        // update contact
        $contact = new Contact($id, $fname, $mname, $lname, $email, $pmobile, $amobile, $avatar, null, $userid);
        $data    = $contact->toArray();
        $updated = $this->dbcon->update('myc_contacts', $data, $conditions);
        if (!$updated) {
            $response = new Response(500, 'Error', ['message' => 'Error updating contact, please try again later']);
            return $response->toJson();
        }
        $response = new Response(200, 'Success', ['message' => 'Contact updated successfully']);
        return $response->toJson();
    }
    public function delete()
    {
        $errors = [];
        $id     = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
        if (empty($id) || !is_numeric($id) || $id <= 0) {
            $errors['id'] = 'Invalid contact';
            $response     = new Response(400, 'Error', $errors);
            return $response->toJson();
        }
        $user       = UtilityFunctions::getSessionUser();
        $userid     = $user['userid'];
        $conditions = "id = $id AND userid = " . $userid;
        $deleted    = $this->dbcon->delete('myc_contacts', $conditions);
        if (!$deleted) {
            $response = new Response(500, 'Error', ['message' => 'Error deleting contact, please try again later']);
            return $response->toJson();
        }
        $response = new Response(200, 'Success', ['message' => 'Contact deleted successfully']);
        return $response->toJson();
    }
    public function upload()
    {
        if (!isset($_FILES['file'])) {
            $errors['form'] = 'file not found';
            $response       = new Response(400, 'Error', $errors);
            return $response->toJson();
        }
        $uploadDir    = 'uploads/'; // Directory to save uploaded files
        $uploadedFile = $uploadDir . time() . '_' . basename($_FILES['file']['name']);
        if (!move_uploaded_file($_FILES['file']['tmp_name'], $uploadedFile)) {
            $errors['form'] = 'file not found';
            $response       = new Response(400, 'Error', $errors);
            return $response->toJson();
        }
        $response = new Response(200, 'Success', ['message' => 'file uploaded successfully','file' => $uploadedFile]);
        return $response->toJson();
    }

    public function deleteavatar()
    {
        $imageSrc = $_POST['src'];
        if (!empty($imageSrc) && file_exists($imageSrc)) {
            unlink($imageSrc);
            $response = new Response(200, 'Success', ['message' => 'file deleted successfully']);
            return $response->toJson();
        } else {
            $errors['form'] = 'file not found';
            $response       = new Response(400, 'Error', $errors);
            return $response->toJson();
        }
    }
}
