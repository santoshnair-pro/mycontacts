<?php

final class SeedContacts
{
    private $table     = 'myc_contacts';
    private $userTable = 'myc_users';
    private $data      = [
        [
            'firstname'        => 'Amit',
            'middlename'       => 'Dhanraj',
            'lastname'         => 'Sahare',
            'email'            => 'amit.saha@example.com',
            'primary_mobile'   => '919766554321',
            'alternate_mobile' => '',
            'avatar'           => '',
            'created_at'       => '',
            'userid'           => '',
        ],
        [
            'firstname'        => 'Mangesh',
            'middlename'       => 'Karan',
            'lastname'         => 'Kode',
            'email'            => 'mangesh.kode@example.com',
            'primary_mobile'   => '919122543367',
            'alternate_mobile' => '',
            'avatar'           => '',
            'created_at'       => '',
            'userid'           => '',
        ],
        [
            'firstname'        => 'Priti',
            'middlename'       => 'Kanchan',
            'lastname'         => 'Paniker',
            'email'            => 'upriti.p@example.com',
            'primary_mobile'   => '919877436511',
            'alternate_mobile' => '',
            'avatar'           => '',
            'created_at'       => '',
            'userid'           => '',
        ],
        [
            'firstname'        => 'Kailash',
            'middlename'       => 'Gopidhar',
            'lastname'         => 'Kanphade',
            'email'            => 'kailash.k@example.com',
            'primary_mobile'   => '918134556121',
            'alternate_mobile' => '',
            'avatar'           => '',
            'created_at'       => '',
            'userid'           => '',
        ],
        [
            'firstname'        => 'Dhiraj',
            'middlename'       => 'Lalit',
            'lastname'         => 'Modi',
            'email'            => 'dhiraj.modi@example.com',
            'primary_mobile'   => '918123454332',
            'alternate_mobile' => '',
            'avatar'           => '',
            'created_at'       => '',
            'userid'           => '',
        ],
        [
            'firstname'        => 'Laksh',
            'middlename'       => 'Kamesh',
            'lastname'         => 'Rao',
            'email'            => 'laksh.rao@example.com',
            'primary_mobile'   => '918798732212',
            'alternate_mobile' => '',
            'avatar'           => '',
            'created_at'       => '',
            'userid'           => '',
        ],
        [
            'firstname'        => 'Leena',
            'middlename'       => 'Prakash',
            'lastname'         => 'Mohite',
            'email'            => 'leena.mohite@example.com',
            'primary_mobile'   => '917866541123',
            'alternate_mobile' => '',
            'avatar'           => '',
            'created_at'       => '',
            'userid'           => '',
        ],
        [
            'firstname'        => 'Suraj',
            'middlename'       => 'Gopal',
            'lastname'         => 'Joshi',
            'email'            => 'suraj.joshi@example.com',
            'primary_mobile'   => '919877542314',
            'alternate_mobile' => '',
            'avatar'           => '',
            'created_at'       => '',
            'userid'           => '',
        ],
        [
            'firstname'        => 'Skehkhar',
            'middlename'       => 'Lalit',
            'lastname'         => 'Goyal',
            'email'            => 'shekh.goyal@example.com',
            'primary_mobile'   => '916755447623',
            'alternate_mobile' => '',
            'avatar'           => '',
            'created_at'       => '',
            'userid'           => '',
        ],
        [
            'firstname'        => 'Samarth',
            'middlename'       => 'kailash',
            'lastname'         => 'Ganveer',
            'email'            => 'samar.ganveer@example.com',
            'primary_mobile'   => '918766789123',
            'alternate_mobile' => '',
            'avatar'           => '',
            'created_at'       => '',
            'userid'           => '',
        ],
        [
            'firstname'        => 'Suman',
            'middlename'       => 'Suraj',
            'lastname'         => 'Pillai',
            'email'            => 'suman.pillai@example.com',
            'primary_mobile'   => '916534227865',
            'alternate_mobile' => '',
            'avatar'           => '',
            'created_at'       => '',
            'userid'           => '',
        ],
        [
            'firstname'        => 'Pariniti',
            'middlename'       => 'Kishor',
            'lastname'         => 'Lonare',
            'email'            => 'pari.lonare@example.com',
            'primary_mobile'   => '918765675432',
            'alternate_mobile' => '',
            'avatar'           => '',
            'created_at'       => '',
            'userid'           => '',
        ],
        [
            'firstname'        => 'Pankaj',
            'middlename'       => 'Vishwa',
            'lastname'         => 'Tripathy',
            'email'            => 'pankaj.tripathy@example.com',
            'primary_mobile'   => '917654432132',
            'alternate_mobile' => '',
            'avatar'           => '',
            'created_at'       => '',
            'userid'           => '',
        ],
    ];

    public function up($db)
    {
        // fetch userid from db for the data
        $result = $db->read($this->userTable, 'id IS NOT NULL', 'id ASC', '0,1');
        if ($result->num_rows) {
            $user = $result->fetch_assoc();
            foreach ($this->data as $row) {
                $row['userid']     = $user['id'];
                $row['created_at'] = date('Y-m-d H:i:s');
                $db->create($this->table, $row);
            }
        }
    }

    public function down($db)
    {
        $db->query('TRUNCATE TABLE `' . (string) $this->table . '`');
    }
}
