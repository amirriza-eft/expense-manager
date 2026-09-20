<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_transactions extends CI_Migration
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

            'category_id' => [
                'type' => 'INT',
                'unsigned' => TRUE
            ],

            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255
            ],

            'amount' => [
                'type' => 'BIGINT'
            ],

            'type' => [
                'type' => 'ENUM',
                'constraint' => ['income', 'expense']
            ],

            'transaction_date' => [
                'type' => 'DATE'
            ],

            'description' => [
                'type' => 'TEXT',
                'null' => TRUE
            ],

            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
            'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ON UPDATE CURRENT_TIMESTAMP',
            'deleted_at DATETIME NULL'
        ]);

        $this->dbforge->add_key('id', TRUE);

        $this->dbforge->add_field(
            'CONSTRAINT fk_transaction_user
            FOREIGN KEY (user_id)
            REFERENCES users(id)
            ON DELETE CASCADE'
        );

        $this->dbforge->add_field(
            'CONSTRAINT fk_transaction_category
            FOREIGN KEY (category_id)
            REFERENCES categories(id)
            ON DELETE CASCADE'
        );

        $this->dbforge->create_table('transactions');
    }

    public function down()
    {
        $this->dbforge->drop_table('transactions');
    }
}