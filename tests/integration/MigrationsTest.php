<?php

namespace App\Tests\Integration;

use PHPUnit\Framework\TestCase;
use App\config\DatabaseConnection;

final class MigrationsTest extends TestCase
{
    public function testMigrations(): void
    {
        // make database connection
        $db = new DatabaseConnection();
        // loop through migrations directory
        $directory = str_replace('/tests/integration', '', __DIR__) . '/database/migrations';
        // use scandir to read files in ascending order
        $files = scandir($directory);
        $files = array_diff($files, ['.', '..']);
        // drops all tables from database if any exist
        $sql = 'SET FOREIGN_KEY_CHECKS = 0';
        $db->query($sql);
        // loop and call up method to drop the table if already exists
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                require_once (string) $directory . '/' . $file;
                $className = '';
                $arr       = explode('_', str_replace('.php', '', basename($file)));
                for ($i = 1; $i < count($arr); $i++) {
                    $className .= ucfirst($arr[$i]);
                }
                if (class_exists($className)) {
                    $migration = new $className();
                    $migration->down($db);
                }
            }
        }
        $sql = 'SET FOREIGN_KEY_CHECKS = 1';
        $db->query($sql);
        // loop and call up method to create the table structure
        $tablesCreated = 0;
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                require_once $directory . '/' . $file;
                $className = '';
                $arr       = explode('_', str_replace('.php', '', basename($file)));
                for ($i = 1; $i < count($arr); $i++) {
                    $className .= ucfirst($arr[$i]);
                }
                if (class_exists($className)) {
                    $migration = new $className();
                    $migration->up($db);
                    ++$tablesCreated;
                }
            }
        }
        // count number of tables in the database
        $sql              = "SELECT table_name FROM information_schema.tables WHERE table_schema = '" . $_ENV['DB_NAME'] . "'";
        $result           = $db->query($sql);
        $tablesInDatabase = $result->num_rows;
        // assert that the number of tables created matches the number of tables in the database
        $this->assertEquals($tablesCreated, $tablesInDatabase);
    }
}
