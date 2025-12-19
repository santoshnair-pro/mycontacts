<?php

namespace App\controllers;

use App\config\DatabaseConnection;
use App\util\AppLogger;

class BaseController
{
    protected $dbcon;
    protected $logger;
    public function __construct()
    {
        // database connection for global use
        $this->dbcon = new DatabaseConnection();
        // initialize application logger
        $this->logger = new AppLogger();
    }
}
