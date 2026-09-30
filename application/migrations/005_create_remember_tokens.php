<?php defined('BASEPATH') or exit('No direct script access allowed');

class Migration_Create_remember_tokens extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'BIGINT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],

            'user_id' => [
                'type' => 'INT',
                'unsigned' => TRUE
            ],

            'selector' => [
                'type' => 'VARCHAR',
                'constraint' => 32
            ],

            'token_hash' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],

            'expires_at' => [
                'type' => 'DATETIME'
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => TRUE
            ]
        ]);

        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->add_key('user_id');
        $this->dbforge->add_key('selector', TRUE);

        $this->dbforge->create_table('remember_tokens', TRUE);
    }

    public function down()
    {
        $this->dbforge->drop_table('remember_tokens', TRUE);
    }
}