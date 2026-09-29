<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Base_API_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        $this->load->library('form_validation');
    }

    protected function json($data, $code = 200)
    {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function auth_user()
    {
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {

            $this->json([
                'status'=>false,
                'message'=>'ابتدا وارد شوید'
            ],401);

        }

        return $user_id;
    }
}