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

    }

    public function summary()
    {
        $user_id = $this->auth_user();

        $budget =
            $this->Budget_model
                ->get_user_budget($user_id);

        return $this->json([

            'status'=>true,

            'budget_amount'=>
                $budget->amount ?? 0,

            'monthly_income'=>
                $this->Transaction_model
                    ->get_monthly_income($user_id),

            'monthly_expense'=>
                $this->Transaction_model
                    ->get_monthly_expense($user_id)
        ]);
    }
}