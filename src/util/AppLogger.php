<?php

namespace App\util;

require './vendor/autoload.php';

use Monolog\Logger;
use Monolog\Level;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\JsonFormatter;

final class AppLogger extends Logger
{
    public function __construct()
    {
        $loggerName = (isset($_ENV['LOGGER_NAME'])) ? $_ENV['LOGGER_NAME'] : 'my_contacts';
        parent::__construct($loggerName);
        $formatter        = new JsonFormatter();
        $logfile          = ($_ENV['LOGGER_NAME'] ? $_ENV['LOGGER_NAME'] : 'my_contacts') . '.log';
        $loglevel         = $_ENV['LOG_LEVEL'] ? $_ENV['LOG_LEVEL'] : 'Debug';
        $level            = constant(Level::class . '::' . ucfirst($loglevel));
        $rotating_handler = new RotatingFileHandler("logs/$logfile", 30, $level);
        $rotating_handler->setFormatter($formatter);
        $this->pushHandler($rotating_handler);
    }

    public function logInfo($message)
    {
        $this->info($message);
    }

    public function logWarning($message)
    {
        $this->warning($message);
    }

    public function logError($message)
    {
        $this->error($message);
    }

    public function logEmergency($message)
    {
        $this->emergency($message);
    }
}
