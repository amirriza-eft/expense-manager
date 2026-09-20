<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_budgets extends CI_Migration
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
            'amount' => [
                'type' => 'BIGINT'
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP',
            'deleted_at DATETIME NULL'
        ]);

        $this->dbforge->add_key('id', TRUE);

        $this->dbforge->add_field(
            'CONSTRAINT fk_budget_user
            FOREIGN KEY (user_id)
            REFERENCES users(id)
            ON DELETE CASCADE'
        );

        $this->dbforge->create_table('budgets');
    }

    public function down()
    {
        $this->dbforge->drop_table('budgets');
    }
}