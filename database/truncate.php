<?php

require 'vendor/autoload.php';
use App\config\DatabaseConnection;

try {
    // load environment variables from file
    $dotenv = Dotenv\Dotenv::createImmutable(str_replace('/database', '', __DIR__));
    $dotenv->load();
    // make database connection
    $db = new DatabaseConnection();
    // loop through seeders directory
    $directory = __DIR__ . '/seeders';
    $dir       = new DirectoryIterator($directory);
    // use scandir to read files in ascending order
    $files = scandir($directory);
    $files = array_diff($files, ['.', '..']);
    // loop and call up method to insert the data into tables
    $db->query('SET FOREIGN_KEY_CHECKS=0'); //disable foreign key checks
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
                $seeder->down($db); // Assuming the up() method creates the table
            }
        }
    }
    $db->query('SET FOREIGN_KEY_CHECKS=1'); //enable foreign key checks
    echo 'DB truncated successfully' . PHP_EOL;
} catch (Exception $e) {
    echo 'Unable to truncate database' . PHP_EOL;
    echo '' . $e->getMessage() . '';
}
