<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Base_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        $this->load->model('User_model');

        $this->authCheck();
    }

    protected function authCheck()
    {
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            exit;
        }
    }
}
