<?php

final class CreateUserSessionsTable
{
    private $table = 'myc_user_sessions';

    public function up($db)
    {
        $sql = 'CREATE TABLE IF  NOT EXISTS `myc_user_sessions` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `userid` bigint unsigned DEFAULT NULL,
            `ipaddress` varchar(45) NOT NULL,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `expires_at` timestamp NOT NULL,
            PRIMARY KEY (`id`),
            CONSTRAINT `myc_user_sessions` FOREIGN KEY (`userid`) REFERENCES `myc_users` (`id`))';
        $db->query($sql);
        echo $this->table . ' created' . PHP_EOL;
    }

    public function down($db)
    {
        $sql = 'DROP TABLE IF EXISTS ' . $this->table;
        $db->query($sql);
        echo $this->table . ' dropped' . PHP_EOL;
    }
}
