<?php

// autoload dependencies
require '../vendor/autoload.php';
// load environment variables for testing
$dotenv = Dotenv\Dotenv::createImmutable(str_replace('/tests', '', __DIR__), '.env.test');
$dotenv->load();
