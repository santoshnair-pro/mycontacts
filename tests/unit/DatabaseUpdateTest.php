<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\config\DatabaseConnection;

final class DatabaseUpdateTest extends TestCase
{
    public function testDatabaseUpdate(): void
    {
        $db   = new DatabaseConnection();
        $data = [
            'fname' => 'UpdatedTest',
            'lname' => 'UpdatedUser',
        ];
        $result = $db->update('myc_users', $data, "email='test.user@example.com'");
        $this->assertTrue($result);
    }
}
