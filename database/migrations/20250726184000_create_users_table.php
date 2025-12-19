<?php

final class CreateUsersTable
{
    private $table = 'myc_users';

    public function up($db)
    {
        $sql = 'CREATE TABLE IF NOT EXISTS `' . $this->table . "` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `fname` varchar(20) NOT NULL,
            `lname` varchar(25) NOT NULL,
            `email` varchar(100) NOT NULL,
            `password` varchar(150) NOT NULL,
            `verified` tinyint(1) NOT NULL DEFAULT '0',
            `verified_at` timestamp NULL DEFAULT NULL,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `myc_users_uni_email` (`email`)
        )";
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
