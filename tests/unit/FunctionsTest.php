<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\util\UtilityFunctions;

final class FunctionsTest extends TestCase
{
    public function testEmailValidation()
    {
        $email  = 'test.test.com';
        $result = UtilityFunctions::validateEmail($email);
        $this->assertArrayIsEqualToArrayIgnoringListOfKeys(
            $result,
            [
                'email' => 'Invalid email address',
            ],
            []
        );
    }

    public function testPhoneValidation()
    {
        $phone  = '12345abc';
        $result = UtilityFunctions::validatePhone($phone);
        $this->assertFalse($result != 0);
    }

    public function testPasswordLengthValidation()
    {
        $password = 'pass';
        $result   = UtilityFunctions::validatePassword($password);
        $this->assertArrayIsEqualToArrayIgnoringListOfKeys(
            $result,
            [
                'password' => 'Your password must be 8 to 20 characters',
            ],
            []
        );
    }

    public function testPasswordNumberValidation()
    {
        $password = 'password';
        $result   = UtilityFunctions::validatePassword($password);
        $this->assertArrayIsEqualToArrayIgnoringListOfKeys(
            $result,
            [
                'password' => 'Your password must contain atleast 1 number',
            ],
            []
        );
    }

    public function testPasswordCapsValidation()
    {
        $password = 'pass101word';
        $result   = UtilityFunctions::validatePassword($password);
        $this->assertArrayIsEqualToArrayIgnoringListOfKeys(
            $result,
            [
                'password' => 'Your password must contain atleast 1 capital letter',
            ],
            []
        );
    }

    public function testPasswordLowerValidation()
    {
        $password = 'PASS101WORD';
        $result   = UtilityFunctions::validatePassword($password);
        $this->assertArrayIsEqualToArrayIgnoringListOfKeys(
            $result,
            [
                'password' => 'Your password must contain atleast 1 lowercase letter',
            ],
            []
        );
    }

    public function testPasswordSpecialCharacterValidation()
    {
        $password = 'PASS101word';
        $result   = UtilityFunctions::validatePassword($password);
        $this->assertArrayIsEqualToArrayIgnoringListOfKeys(
            $result,
            [
                'password' => 'Your password Must Contain At Least 1 special character',
            ],
            []
        );
    }

    public function testPasswordValidation()
    {
        $password = 'Pass10$#@worD';
        $result   = UtilityFunctions::validatePassword($password);
        $this->assertArrayIsEqualToArrayIgnoringListOfKeys(
            $result,
            [],
            []
        );
    }
}
