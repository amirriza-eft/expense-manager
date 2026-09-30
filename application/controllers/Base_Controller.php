<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Base_Controller extends CI_Controller
{
    protected $auth_service;

    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        $this->load->model('User_model');
        $this->load->model('Remember_token_model');

        require_once APPPATH . 'services/Auth_service.php';

        $this->auth_service = new Auth_service(
            $this->User_model,
            $this->session,
            $this->Remember_token_model
        );

        $this->auth_service->restore_remembered_session();

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