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
                $seeder->up($db); // Assuming the up() method creates the table
            }
        }
    }
    echo 'DB seeders successfully executed' . PHP_EOL;
} catch (Exception $e) {
    echo 'Unable to run seeders' . PHP_EOL;
    echo '' . $e->getMessage() . '';
}
