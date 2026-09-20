<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migrate extends CI_Controller
{
    public function latest()
    {
        $this->load->library('migration');

        if ($this->migration->latest() === FALSE) {
            echo $this->migration->error_string();
            return;
        }

        echo 'Migration completed successfully.';
    }

    public function rollback()
    {
        $this->load->library('migration');

        if ($this->migration->version(0) === FALSE) {
            echo $this->migration->error_string();
            return;
        }

        echo 'Migrations rolled back successfully.';
    }
}