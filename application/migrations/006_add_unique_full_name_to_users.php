<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_unique_full_name_to_users extends CI_Migration
{
    public function up()
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
                'Cannot add unique full_name index. Duplicate names exist: '
                . implode(', ', $names)
            );
        }

        // TRIM-based uniqueness so whitespace-only variants cannot bypass the rule.
        $this->db->query(
            'ALTER TABLE `users`
             ADD COLUMN `full_name_normalized` VARCHAR(100)
             GENERATED ALWAYS AS (TRIM(`full_name`)) STORED,
             ADD UNIQUE KEY `full_name_normalized` (`full_name_normalized`)'
        );
    }

    public function down()
    {
        $this->db->query(
            'ALTER TABLE `users`
             DROP INDEX `full_name_normalized`,
             DROP COLUMN `full_name_normalized`'
        );
    }
}
