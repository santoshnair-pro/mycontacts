<?php

namespace App\Tests\Integration;

use PHPUnit\Framework\TestCase;
use App\config\DatabaseConnection;

final class DatabaseConnectionTest extends TestCase
{
    public function testDatabaseConnection(): void
    {
        $conn = new DatabaseConnection();
        $this->assertNotNull($conn->connection);
    }
}
