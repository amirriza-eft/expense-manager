<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_users extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],

            'full_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],

            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],

            'password' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],

            'avatar' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => TRUE
            ],

            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',

            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP',

            'deleted_at DATETIME NULL'
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('email', FALSE, TRUE);

        $this->dbforge->create_table('users');
    }

    public function down()
    {
        $this->dbforge->drop_table('users');
    }
}