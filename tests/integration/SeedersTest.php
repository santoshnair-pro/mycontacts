<?php

namespace App\Tests\Integration;

use PHPUnit\Framework\TestCase;
use App\config\DatabaseConnection;
use DirectoryIterator;

final class SeedersTest extends TestCase
{
    public function testSeeders(): void
    {
        // make database connection
        $db = new DatabaseConnection();
        // loop through seeders directory
        $directory = str_replace('/tests/integration', '', __DIR__) . '/database/seeders';
        $dir       = new DirectoryIterator($directory);
        // use scandir to read files in ascending order
        $files = scandir($directory);
        $files = array_diff($files, ['.', '..']);
        // truncate all tables in the database to avoid duplicate entries
        $sql = 'SET FOREIGN_KEY_CHECKS = 0';
        $db->query($sql);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                require_once $directory . '/' . $file;
                $className = '';
                $arr       = explode('_', str_replace('.php', '', basename($file)));
                for ($i = 1; $i < count($arr); $i++) {
                    $className .= ucfirst($arr[$i]);
                }
                if (class_exists($className)) {
                    $seeder = new $className();
                    $seeder->down($db);
                }
            }
        }
        $sql = 'SET FOREIGN_KEY_CHECKS = 1';
        $db->query($sql);
        // loop and call up method to insert the data into tables
        $tablesSeeded = 0;
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                require_once $directory . '/' . $file;
                $className = '';
                $arr       = explode('_', str_replace('.php', '', basename($file)));
                for ($i = 1; $i < count($arr); $i++) {
                    $className .= ucfirst($arr[$i]);
                }
                if (class_exists($className)) {
                    $seeder = new $className();
                    $seeder->up($db);
                    ++$tablesSeeded;
                }
            }
        }
        $this->assertEquals($tablesSeeded, count($files));
    }
}
