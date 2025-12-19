<?php

namespace App\controllers;

use App\util\UtilityFunctions;
use App\util\Response;
use App\models\User;
use DateTime;

final class UserController extends BaseController
{
    public function __construct()
    {
        // get database connection from parent class
        parent::__construct();
    }
    public function login()
    {
        $email      = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
        $password   = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
        $errors     = [];
        $emailError = UtilityFunctions::validateEmail($email);
        if (!empty($emailError)) {
            $errors = array_merge($errors, $emailError);
        }
        $passwordError = UtilityFunctions::validatePassword($password);
        if (!empty($passwordError)) {
            $errors = array_merge($errors, $passwordError);
        }
        if (!empty($errors)) {
            $response = new Response(400, 'Error', $errors);
            return $response->toJson();
        }
        $result = $this->dbcon->read('myc_users', "email = '" . $email . "'");
        if (!$result->num_rows) {
            $errors['email'] = 'user not registered';
            $response        = new Response(400, 'Error', $errors);
            return $response->toJson();
        }
        $user           = $result->fetch_assoc();
        $hashedPassword = $user['password'];
        // compare password
        $passwordValid = password_verify($password, $hashedPassword);
        if (!$passwordValid) {
            $errors['password'] = 'Invalid username/password';
            $response           = new Response(400, 'Error', $errors);
            return $response->toJson();
        }
        //password validation successful return authToken for the session
        $expiresAt = new DateTime()->modify('+24 hours')->format('YmdHis');
        $data      = $user['id'] . '|' . $user['fname'] . '|' . $user['lname'] . '_' . $_SERVER['REMOTE_ADDR'] . '_' . $expiresAt;
        $method    = UtilityFunctions::ENCRYPT_METHOD;
        $key       = $_ENV['TOKEN_HASH'];
        $options   = UtilityFunctions::ENCRYPT_OPTIONS;
        $iv        = UtilityFunctions::ENCRYPT_IV;
        $authToken = openssl_encrypt($data, $method, $key, $options, $iv);
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        // insert data into user sessions table
        $this->dbcon->create('myc_user_sessions', [
            'userid'     => $user['id'],
            'ipaddress'  => $_SERVER['REMOTE_ADDR'],
            'expires_at' => $expiresAt,
        ]);
        $_SESSION['authToken'] = $authToken;
        // start session and send authtoken for user session
        $response = new Response(200, 'Success', ['message' => 'Successfully logged in']);
        return $response->toJson();
    }
    public function logout()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['authToken'])) {
            unset($_SESSION['authToken']);
        }
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        // destroy session
        session_destroy();
        $response = new Response(200, 'Success', ['message' => 'Logged out successfully']);
        return $response->toJson();
    }
    public function create()
    {
        $fname     = filter_var($_POST['fname'], FILTER_SANITIZE_STRING);
        $lname     = filter_var($_POST['lname'], FILTER_SANITIZE_STRING);
        $email     = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
        $password  = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);
        $cpassword = filter_var($_POST['cpass'], FILTER_SANITIZE_STRING);
        $errors    = [];
        if (strlen($fname) < 2 || strlen($fname) > 20) {
            $errors['fname'] = 'First name must be at least 2 characters long and maximum 20 characters long';
        }
        if (strlen($lname) < 2 || strlen($lname) > 25) {
            $errors['lname'] = 'Last name must be at least 2 characters long and maximum 25 characters long';
        }
        $emailError = UtilityFunctions::validateEmail($email);
        if (!empty($emailError)) {
            $errors = array_merge($errors, $emailError);
        }
        $passwordError = UtilityFunctions::validatePassword($password);
        if (!empty($passwordError)) {
            $errors = array_merge($errors, $passwordError);
        }
        if ($password !== $cpassword) {
            $errors['confirm'] = 'Password and Confirm Password do not match';
        }
        if (!empty($errors)) {
            $response = new Response(400, 'Error', $errors);
            return $response->toJson();
        }
        //check if email already exists
        $result = $this->dbcon->read('myc_users', "email = '" . $email . "'");
        if ($result->num_rows) {
            $errors['email'] = 'Email already registered';
            $response        = new Response(400, 'Error', $errors);
            return $response->toJson();
        }
        //validation successful, create user
        $hashedPassword = password_hash($password, PASSWORD_ARGON2I);
        $user           = new User(null, $fname, $lname, $email, $hashedPassword, 1, (new DateTime())->format('Y-m-d H:i:s'), (new DateTime())->format('Y-m-d H:i:s'));
        $data           = $user->toArray();
        $userId         = $this->dbcon->create('myc_users', $data);
        if (!$userId) {
            $response = new Response(500, 'Error', ['message' => 'Error creating user, please try again later']);
            return $response->toJson();
        }
        $response = new Response(200, 'Success', ['message' => 'User registered successfully']);
        return $response->toJson();
    }
    public function update()
    {
        $fname = filter_var($_POST['fname'], FILTER_SANITIZE_STRING);
        $lname = filter_var($_POST['lname'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
        if (strlen($fname) < 2 || strlen($fname) > 20) {
            $errors['fname'] = 'First name must be at least 2 characters long and maximum 20 characters long';
        }
        if (strlen($lname) < 2 || strlen($lname) > 25) {
            $errors['lname'] = 'Last name must be at least 2 characters long and maximum 25 characters long';
        }
        $emailError = UtilityFunctions::validateEmail($email);
        if (!empty($emailError)) {
            $errors = array_merge($errors, $emailError);
        }
        if (!empty($errors)) {
            $response = new Response(400, 'Error', $errors);
            return $response->toJson();
        }
        $user = UtilityFunctions::getSessionUser();

        $result = $this->dbcon->read('myc_users', "id = '" . $user['userid'] . "'");
        if (!$result->num_rows) {
            $errors['form'] = 'user not found';
            $response       = new Response(400, 'Error', $errors);
            return $response->toJson();
        }
        $userData = $result->fetch_assoc();
        if ($email !== $userData['email']) {
            //check if email belongs to another user
            $result = $this->dbcon->read('myc_users', "email = '" . $email . "' AND id != '" . $user['userid'] . "'");
            if ($result->num_rows) {
                $errors['email'] = 'Email already registered by another user';
                $response        = new Response(400, 'Error', $errors);
                return $response->toJson();
            }
        }
        // validation succesful, update user
        $updateResult = $this->dbcon->update('myc_users', [
            'fname' => $fname,
            'lname' => $lname,
            'email' => $email,
        ], "id = '" . $user['userid'] . "'");
        if (!$updateResult) {
            $response = new Response(500, 'Error', ['message' => 'Error updating user, please try again later']);
            return $response->toJson();
        }
        // update session user data
        $user['fname'] = $fname;
        $user['lname'] = $lname;
        UtilityFunctions::setSessionUser($user);
        $response = new Response(200, 'Success', ['message' => 'User updated successfully']);
        return $response->toJson();
    }
    public function changePassword()
    {
        $currentPassword = filter_var($_POST['epass'], FILTER_SANITIZE_STRING);
        $newPassword     = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);
        $confirmPassword = filter_var($_POST['cpass'], FILTER_SANITIZE_STRING);
        $errors          = [];
        $passwordError   = UtilityFunctions::validatePassword($newPassword);
        if (!empty($passwordError)) {
            $errors = array_merge($errors, $passwordError);
        }
        if ($newPassword !== $confirmPassword) {
            $errors['confirm'] = 'New Password and Confirm Password do not match';
        }
        if (!empty($errors)) {
            $response = new Response(400, 'Error', $errors);
            return $response->toJson();
        }
        //fetch loggedin user deails
        $user = UtilityFunctions::getSessionUser();

        $result = $this->dbcon->read('myc_users', "id = '" . $user['userid'] . "'");
        if (!$result->num_rows) {
            $errors['form'] = 'user not found';
            $response       = new Response(400, 'Error', $errors);
            return $response->toJson();
        }
        $userData       = $result->fetch_assoc();
        $hashedPassword = $userData['password'];
        // compare current password
        $passwordValid = password_verify($currentPassword, $hashedPassword);
        if (!$passwordValid) {
            $errors['epass'] = 'The password provided does not match with current password';
            $response        = new Response(400, 'Error', $errors);
            return $response->toJson();
        }
        // check if current and new password are same
        if ($currentPassword === $newPassword) {
            $errors['password'] = 'New password cannot be same as current password';
            $response           = new Response(400, 'Error', $errors);
            return $response->toJson();
        }
        //update password
        $newHashedPassword = password_hash($newPassword, PASSWORD_ARGON2I);
        $updateResult      = $this->dbcon->update('myc_users', ['password' => $newHashedPassword], "id = '" . $user['userid'] . "'");
        if (!$updateResult) {
            $response = new Response(500, 'Error', ['message' => 'Error updating password, please try again later']);
            return $response->toJson();
        }
        $response = new Response(200, 'Success', ['message' => 'Password updated successfully']);
        return $response->toJson();
    }

    public function forgotPassword()
    {
        $email      = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
        $errors     = [];
        $emailError = UtilityFunctions::validateEmail($email);
        if (!empty($emailError)) {
            $errors = array_merge($errors, $emailError);
        }
        if (!empty($errors)) {
            $response = new Response(400, 'Error', $errors);
            return $response->toJson();
        }
        $response = new Response(200, 'Success', ['message' => 'We have sent password reset instructions to your email address if it is registered with us.']);
        return $response->toJson();
    }
}
