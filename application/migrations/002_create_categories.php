<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_categories extends CI_Migration
{
    public function up()
    {
        $this->dbforge->add_field([
            'id' => [
                'type' => 'INT',
                'unsigned' => TRUE,
                'auto_increment' => TRUE
            ],

            'user_id' => [
                'type' => 'INT',
                'unsigned' => TRUE
            ],

            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 100
            ],

            'type' => [
                'type' => 'ENUM',
                'constraint' => ['income', 'expense']
            ],

            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',

            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP',

            'deleted_at DATETIME NULL'
        ]);

        $this->dbforge->add_key('id', TRUE);

        $this->dbforge->add_field(
            'CONSTRAINT fk_category_user
            FOREIGN KEY (user_id)
            REFERENCES users(id)
            ON DELETE CASCADE'
        );

        $this->dbforge->create_table('categories');
    }

    public function down()
    {
        $this->dbforge->drop_table('categories');
    }
}