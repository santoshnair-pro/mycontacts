<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\config\DatabaseConnection;

final class DatabaseInsertTest extends TestCase
{
    public function testDatabaseInsert(): void
    {
        // make database connection
        $db = new DatabaseConnection();

        $db->delete('myc_users', "email='test.user@example.com'");

        $data = [
            'fname'       => 'Test',
            'lname'       => 'User',
            'email'       => 'test.user@example.com',
            'password'    => password_hash('Test$#@2025User', PASSWORD_ARGON2I),
            'verified'    => '1',
            'verified_at' => date('Y-m-d H:i:s'),
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $result = $db->create('myc_users', $data);
        $this->assertTrue($result);
    }
}
