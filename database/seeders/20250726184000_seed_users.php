<?php

final class SeedUsers
{
    private string $table = 'myc_users';
    private array $data   = [
        [
            'fname'       => 'Roger',
            'lname'       => 'Cooper',
            'email'       => 'roger.cooper@example.com',
            'password'    => 'Rog$#@2025Per',
            'verified'    => '1',
            'verified_at' => '',
            'created_at'  => '',
        ],
    ];

    public function up($db)
    {
        foreach ($this->data as $row) {
            $row['verified_at'] = date('Y-m-d H:i:s');
            $row['created_at']  = date('Y-m-d H:i:s');
            $row['password']    = password_hash($row['password'], PASSWORD_ARGON2I);
            $db->create($this->table, $row);
        }
    }

    public function down($db)
    {
        $db->query('TRUNCATE TABLE `' . $this->table . '`');
    }
}
