<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'controllers/api/Base_API_Controller.php');

class Profile_API_Controller extends Base_API_Controller
{
    protected $profile_service;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->library('upload');

        require_once APPPATH . 'services/Profile_service.php';
        require_once APPPATH . 'policies/User_policy.php';

        $this->profile_service = new Profile_service(
            $this->User_model,
            new User_policy(),
            $this->upload,
            $this->session
        );
    }

    public function get()
    {
        $result = $this->profile_service->get(
            $this->session->userdata('user_id')
        );

        return $this->json($result);
    }

    public function update()
    {
        $result = $this->profile_service->update(
            $this->session->userdata('user_id'),
            $this->input->post(),
            $_FILES
        );

        return $this->json($result, $result['code'] ?? 200);
    }

    public function change_password()
    {
        $result = $this->profile_service->change_password(
            $this->session->userdata('user_id'),
            $this->input->post()
        );

        return $this->json($result, $result['code'] ?? 200);
    }
}