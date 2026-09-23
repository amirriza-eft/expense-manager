<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'controllers/Base_Controller.php');

class Home extends Base_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            redirect('login');
        }

        $this->load->model('Budget_model');
        $this->load->model('Transaction_model');

        $budget = $this->Budget_model
            ->get_user_budget($user_id);


        $data = [
            'budget_amount' =>
                $budget ? $budget->amount : 0,

            'monthly_income' =>
                $this->Transaction_model
                    ->get_monthly_income($user_id),

            'monthly_expense' =>
                $this->Transaction_model
                    ->get_monthly_expense($user_id)

        ];

        $data['page_title'] = 'خانه';
        $this->load->view('home', $data);
    }
}