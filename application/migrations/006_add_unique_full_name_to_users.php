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

        $this->db->query(
            'ALTER TABLE `users` ADD UNIQUE KEY `full_name` (`full_name`)'
        );
    }

    public function down()
    {
        $this->db->query(
            'ALTER TABLE `users` DROP INDEX `full_name`'
        );
    }
}
