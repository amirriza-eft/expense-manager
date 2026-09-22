<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Category extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Category_model');
        $this->load->library('session');
    }

    public function index()
    {
        $user_id = $this->session->userdata('user_id');

        if(!$user_id)
        {
            return $this->json([
                'status'=>false,
                'message'=>'Unauthorized'
            ],401);
        }

        $categories = $this->Category_model
            ->get_user_categories($user_id);

        return $this->json([
            'status'=>true,
            'categories'=>$categories
        ]);
    }

    public function create()
    {
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            return $this->json([
                'status' => false,
                'message' => 'ابتدا وارد حساب خود شوید'
            ], 401);
        }

        $data = [
            'user_id' => $user_id,
            'title' => $this->input->post('title', true)
        ];

        if ($this->Category_model->create($data)) {
            return $this->json([
                'status' => true,
                'message' => 'دسته‌بندی با موفقیت ایجاد شد'
            ]);
        }

        return $this->json([
            'status' => false,
            'message' => 'خطا در ایجاد دسته‌بندی'
        ], 500);
    }

    private function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}