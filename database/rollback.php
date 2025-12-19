<?php

require 'vendor/autoload.php';
use App\config\DatabaseConnection;

try {
    // load environment variables from file
    $dotenv = Dotenv\Dotenv::createImmutable(str_replace('/database', '', __DIR__));
    $dotenv->load();
    // make database connection
    $db = new DatabaseConnection();
    // loop through migrations directory
    $directory = __DIR__ . '/migrations';
    // use scandir to read files in ascending order
    $files = scandir($directory);
    $files = array_diff($files, ['.', '..']);
    // loop and call up method to create the table structure
    $db->query('SET FOREIGN_KEY_CHECKS = 0');
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
                $migration->down($db); // Assuming the up() method creates the table
            }
        }
    }
    $db->query('SET FOREIGN_KEY_CHECKS = 1');
    echo 'DB migrations successfully rolledback' . PHP_EOL;
} catch (Exception $e) {
    echo 'Unable to rollback DB migrations' . PHP_EOL;
    echo '' . $e->getMessage() . '';
}
