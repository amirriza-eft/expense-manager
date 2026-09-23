<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'controllers/api/Base_API_Controller.php');

class Transaction extends Base_API_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Transaction_model');
        $this->load->library('session');
    }

    public function index()
    {
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            return $this->json([
                'status' => false,
                'message' => 'ابتدا وارد شوید'
            ], 401);
        }


        $page = (int)$this->input->get('page') ?: 1;
        $limit = 8;
        $offset = ($page - 1) * $limit;


        $transactions = $this->Transaction_model
            ->get_user_transactions($user_id, $limit, $offset);


        $total = $this->Transaction_model
            ->count_user_transactions($user_id);


        return $this->json([
            'status' => true,
            'transactions' => $transactions,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total / $limit),
                'total' => $total
            ]
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

        $category_id = $this->input->post('category_id', true);

        $data = [
            'user_id' => $user_id,
            'category_id' => $category_id ?: null,
            'title' => $this->input->post('title', true),
            'amount' => $this->input->post('amount', true),
            'type' => $this->input->post('type', true),
            'transaction_date' => $this->input->post('transaction_date', true),
            'description' => $this->input->post('description', true)
        ];

        if ($this->Transaction_model->create($data)) {
            return $this->json([
                'status' => true,
                'message' => 'تراکنش با موفقیت ثبت شد'
            ]);
        }

        return $this->json([
            'status' => false,
            'message' => 'خطا در ثبت تراکنش'
        ], 500);
    }

    public function update()
    {
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            return $this->json([
                'status' => false,
                'message' => 'ابتدا وارد شوید'
            ], 401);
        }

        $id = $this->input->post('id', true);

        $data = [
            'category_id' => $this->input->post('category_id', true) ?: null,
            'title' => $this->input->post('title', true),
            'amount' => $this->input->post('amount', true),
            'type' => $this->input->post('type', true),
            'transaction_date' => $this->input->post('transaction_date', true),
            'description' => $this->input->post('description', true)
        ];

        if ($this->Transaction_model->update($id, $user_id, $data)) {
            return $this->json([
                'status' => true,
                'message' => 'تراکنش بروزرسانی شد'
            ]);
        }

        return $this->json([
            'status' => false,
            'message' => 'خطا در بروزرسانی'
        ], 500);
    }


    public function delete()
    {
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            return $this->json([
                'status' => false,
                'message' => 'ابتدا وارد شوید'
            ], 401);
        }

        $id = $this->input->post('id', true);

        if ($this->Transaction_model->delete($id, $user_id)) {
            return $this->json([
                'status' => true,
                'message' => 'تراکنش حذف شد'
            ]);
        }

        return $this->json([
            'status' => false,
            'message' => 'خطا در حذف'
        ], 500);
    }

}