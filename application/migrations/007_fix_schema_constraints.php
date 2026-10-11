<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Fixes schema issues left by earlier migrations:
 * - remember_tokens used a composite PRIMARY KEY (id, selector) because
 *   add_key('selector', TRUE) marks a primary key in CI3 (not unique).
 * - users.email was never unique: CI3 add_key() has no unique flag, so
 *   add_key('email', FALSE, TRUE) only created a normal index.
 * - full_name uniqueness is moved to TRIM(full_name) via a generated
 *   column so whitespace-only variants cannot bypass the rule.
 */
class Migration_Fix_schema_constraints extends CI_Migration
{
    public function up()
    {
        $this->fix_remember_tokens();
        $this->add_unique_email();
        $this->normalize_full_name_unique();
    }

    public function down()
    {
        if ($this->index_exists('users', 'full_name_normalized')) {
            $this->db->query('ALTER TABLE `users` DROP INDEX `full_name_normalized`');
        }

        if ($this->column_exists('users', 'full_name_normalized')) {
            $this->db->query('ALTER TABLE `users` DROP COLUMN `full_name_normalized`');
        }

        if (!$this->index_exists('users', 'full_name')) {
            $this->db->query('ALTER TABLE `users` ADD UNIQUE KEY `full_name` (`full_name`)');
        }

        if ($this->index_exists('users', 'email') && $this->is_unique_index('users', 'email')) {
            $this->db->query('ALTER TABLE `users` DROP INDEX `email`');
            $this->db->query('ALTER TABLE `users` ADD KEY `email` (`email`)');
        }

        if ($this->index_exists('remember_tokens', 'selector')) {
            $this->db->query('ALTER TABLE `remember_tokens` DROP INDEX `selector`');
        }

        if ($this->primary_columns('remember_tokens') === ['id']) {
            $this->db->query(
                'ALTER TABLE `remember_tokens`
                 DROP PRIMARY KEY,
                 ADD PRIMARY KEY (`id`, `selector`)'
            );
        }
    }

    private function fix_remember_tokens()
    {
        $primary_columns = $this->primary_columns('remember_tokens');

        if ($primary_columns === ['id', 'selector'] || $primary_columns === ['selector', 'id']) {
            $this->db->query('ALTER TABLE `remember_tokens` DROP PRIMARY KEY, ADD PRIMARY KEY (`id`)');
        }

        if (!$this->index_exists('remember_tokens', 'selector')) {
            $this->db->query('ALTER TABLE `remember_tokens` ADD UNIQUE KEY `selector` (`selector`)');
        }
    }

    private function add_unique_email()
    {
        if ($this->is_unique_index('users', 'email')) {
            return;
        }

        $duplicates = $this->db
            ->select('email, COUNT(*) AS total')
            ->from('users')
            ->group_by('email')
            ->having('total >', 1)
            ->get()
            ->result();

        if (!empty($duplicates)) {
            $emails = array_map(function ($row) {
                return $row->email;
            }, $duplicates);

            show_error(
                'Cannot add unique email index. Duplicate emails exist: '
                . implode(', ', $emails)
            );
        }

        if ($this->index_exists('users', 'email')) {
            $this->db->query('ALTER TABLE `users` DROP INDEX `email`');
        }

        $this->db->query('ALTER TABLE `users` ADD UNIQUE KEY `email` (`email`)');
    }

    private function normalize_full_name_unique()
    {
        $duplicates = $this->db
            ->select('TRIM(full_name) AS full_name, COUNT(*) AS total', false)
            ->from('users')
            ->group_by('TRIM(full_name)')
            ->having('total >', 1)
            ->get()
            ->result();

        if (!empty($duplicates)) {
            $names = array_map(function ($row) {
                return $row->full_name;
            }, $duplicates);

            show_error(
                'Cannot add normalized full_name unique index. Duplicate names exist: '
                . implode(', ', $names)
            );
        }

        if (!$this->column_exists('users', 'full_name_normalized')) {
            $this->db->query(
                'ALTER TABLE `users`
                 ADD COLUMN `full_name_normalized` VARCHAR(100)
                 GENERATED ALWAYS AS (TRIM(`full_name`)) STORED'
            );
        }

        if (!$this->index_exists('users', 'full_name_normalized')) {
            $this->db->query(
                'ALTER TABLE `users` ADD UNIQUE KEY `full_name_normalized` (`full_name_normalized`)'
            );
        }

        if ($this->index_exists('users', 'full_name')) {
            $this->db->query('ALTER TABLE `users` DROP INDEX `full_name`');
        }
    }

    private function index_exists($table, $index_name)
    {
        $query = $this->db->query(
            'SHOW INDEX FROM `' . $table . '` WHERE Key_name = '
            . $this->db->escape($index_name)
        );

        return $query->num_rows() > 0;
    }

    private function is_unique_index($table, $index_name)
    {
        $query = $this->db->query(
            'SHOW INDEX FROM `' . $table . '` WHERE Key_name = '
            . $this->db->escape($index_name)
            . ' AND Non_unique = 0'
        );

        return $query->num_rows() > 0;
    }

    private function column_exists($table, $column)
    {
        return $this->db
            ->query('SHOW COLUMNS FROM `' . $table . '` LIKE ' . $this->db->escape($column))
            ->num_rows() > 0;
    }

    private function primary_columns($table)
    {
        $query = $this->db->query(
            'SHOW INDEX FROM `' . $table . '` WHERE Key_name = \'PRIMARY\''
        );

        $rows = $query->result();

        usort($rows, function ($a, $b) {
            return ((int) $a->Seq_in_index) - ((int) $b->Seq_in_index);
        });

        $columns = [];

        foreach ($rows as $row) {
            $columns[] = $row->Column_name;
        }

        return $columns;
    }
}
