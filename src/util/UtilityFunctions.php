<?php

namespace App\util;

use App\config\DatabaseConnection;
use DateTime;

final class UtilityFunctions
{
    public const string ENCRYPT_METHOD = 'AES-256-CBC';
    public const string ENCRYPT_IV     = '1312111231098456';
    public const int ENCRYPT_OPTIONS   = 0;
    public static function isAuthenticated(): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['authToken']) || empty($_SESSION['authToken'])) {
            return false;
        }
        // check token expirty and user details here
        $key                       = $_ENV['TOKEN_HASH'];
        $sessionToken              = openssl_decrypt($_SESSION['authToken'], self::ENCRYPT_METHOD, $key, self::ENCRYPT_OPTIONS, self::ENCRYPT_IV);
        [$userData,$ip,$expiresAt] = explode('_', $sessionToken);
        [$userid,$fname,$lname]    = explode('|', $userData);
        // check db for user/password validation
        $dbcon  = new DatabaseConnection();
        $result = $dbcon->read('myc_user_sessions', "userid = '" . $userid . "' AND ipaddress = '" . $ip . "' AND expires_at = '" . $expiresAt . "'");
        if (!$result->num_rows) {
            return false;
        }
        $session         = $result->fetch_assoc();
        $currentDateTime = new DateTime();
        $expiryDateTime  = DateTime::createFromFormat('Y-m-d H:i:s', $session['expires_at']);
        if ($currentDateTime > $expiryDateTime) {
            return false;
        }
        $diff = $expiryDateTime->getTimestamp() - $currentDateTime->getTimestamp();
        if ($diff < 300) { //less than 5 minutes
            //extend session by 30 minutes
            $newExpiryDateTime = $currentDateTime->modify('+30 minutes');
            //update session authtoken
            $sessionToken          = $userid . '|' . $fname . '|' . $lname . '_' . $ip . '_' . $newExpiryDateTime->format('YmdHis');
            $_SESSION['authToken'] = openssl_encrypt($sessionToken, self::ENCRYPT_METHOD, $key, self::ENCRYPT_OPTIONS, self::ENCRYPT_IV);
            $dbcon->update(
                'myc_user_sessions',
                ['expires_at' => $newExpiryDateTime->format('Y-m-d H:i:s')],
                "userid = '" . $userid . "' AND ipaddress = '" . $ip . "' AND expires_at = '" . $expiresAt . "'"
            );
        }
        return true;
    }
    public static function setSessionUser($user)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['authToken']) || empty($_SESSION['authToken'])) {
            return null;
        }
        $key                    = $_ENV['TOKEN_HASH'];
        $sessionToken           = openssl_decrypt($_SESSION['authToken'], self::ENCRYPT_METHOD, $key, self::ENCRYPT_OPTIONS, self::ENCRYPT_IV);
        [$userData]             = explode('_', $sessionToken);
        [$userid,$fname,$lname] = explode('|', $userData);
        $sessionToken           = str_ireplace($userid . '|' . $fname . '|' . $lname, $userid . '|' . $user['fname'] . '|' . $user['lname'], $sessionToken);
        $_SESSION['authToken']  = openssl_encrypt($sessionToken, self::ENCRYPT_METHOD, $key, self::ENCRYPT_OPTIONS, self::ENCRYPT_IV);
        return true;
    }
    public static function getSessionUser()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!isset($_SESSION['authToken']) || empty($_SESSION['authToken'])) {
            return null;
        }
        $key                    = $_ENV['TOKEN_HASH'];
        $sessionToken           = openssl_decrypt($_SESSION['authToken'], self::ENCRYPT_METHOD, $key, self::ENCRYPT_OPTIONS, self::ENCRYPT_IV);
        [$userData]             = explode('_', $sessionToken);
        [$userid,$fname,$lname] = explode('|', $userData);
        return [
            'userid' => $userid,
            'fname'  => $fname,
            'lname'  => $lname,
        ];
    }
    public static function validateEmail(string $email)
    {
        $error = [];
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $error['email'] = 'Invalid email address';
        }
        return $error;
    }

    public static function validatePhone(string $phoneNumber)
    {
        return preg_match('/^\\+?[1-9]\\d{1,14}$/', $phoneNumber);
    }
    public static function validatePassword(string $password)
    {
        $error = [];
        if (strlen($password) < 8 || strlen($password) > 20) {
            $error['password'] = 'Your password must be 8 to 20 characters';
        } elseif (!preg_match('#[0-9]+#', $password)) {
            $error['password'] = 'Your password must contain atleast 1 number';
        } elseif (!preg_match('#[A-Z]+#', $password)) {
            $error['password'] = 'Your password must contain atleast 1 capital letter';
        } elseif (!preg_match('#[a-z]+#', $password)) {
            $error['password'] = 'Your password must contain atleast 1 lowercase letter';
        } elseif (!preg_match("#[\W]+#", $password)) {
            $error['password'] = 'Your password Must Contain At Least 1 special character';
        }
        return $error;
    }
}
