<?php

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\config\DatabaseConnection;

final class DatabaseReadTest extends TestCase
{
    public function testDatabaseRead(): void
    {
        $db     = new DatabaseConnection();
        $result = $db->read('myc_users');
        $this->assertTrue($result->num_rows >= 0);
    }
}
