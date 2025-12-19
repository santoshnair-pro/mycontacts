<?php

final class CreateContactsTable
{
    private $table = 'myc_contacts';

    public function up($db)
    {
        $sql = 'CREATE TABLE IF  NOT EXISTS `myc_contacts` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT,
            `firstname` varchar(20) NOT NULL,
            `middlename` varchar(25) DEFAULT NULL,
            `lastname` varchar(25) NOT NULL,
            `email` varchar(100) DEFAULT NULL,
            `primary_mobile` varchar(15) NOT NULL,
            `alternate_mobile` varchar(15) DEFAULT NULL,
            `avatar` blob,
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `userid` bigint unsigned DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `myc_contacts_user` (`userid`),
            CONSTRAINT `myc_contacts_user` FOREIGN KEY (`userid`) REFERENCES `myc_users` (`id`))';
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
