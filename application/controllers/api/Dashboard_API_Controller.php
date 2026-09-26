<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'controllers/api/Base_API_Controller.php');

class Dashboard_API_Controller extends Base_API_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Budget_model');
        $this->load->model('Transaction_model');
        $this->load->library('session');
    }

    public function summary()
    {
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            return $this->json([
                'status' => false,
                'message' => 'ابتدا وارد شوید'
            ], 401);
        }

        $budget = $this->Budget_model->get_user_budget($user_id);

        return $this->json([
            'status' => true,
            'budget_amount' => $budget ? $budget->amount : 0,
            'monthly_income' => $this->Transaction_model->get_monthly_income($user_id),
            'monthly_expense' => $this->Transaction_model->get_monthly_expense($user_id),
        ]);
    }
}
