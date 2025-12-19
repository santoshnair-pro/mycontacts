<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\config\DatabaseConnection;

final class DatabaseDeleteTest extends TestCase
{
    public function testDatabaseDelete(): void
    {
        // make database connection
        $db     = new DatabaseConnection();
        $result = $db->read('myc_users', "email='delete.user@example.com'");
        if (!$result->num_rows) {
            $data = [
                'fname'       => 'Delete',
                'lname'       => 'User',
                'email'       => 'delete.user@example.com',
                'password'    => password_hash('Test$#@2025User', PASSWORD_ARGON2I),
                'verified'    => '1',
                'verified_at' => date('Y-m-d H:i:s'),
                'created_at'  => date('Y-m-d H:i:s'),
            ];

            $result = $db->create('myc_users', $data);
        }
        $result = $db->delete('myc_users', "email='delete.user@example.com'");
        $this->assertTrue($result);
    }
}
