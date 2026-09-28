<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'controllers/api/Base_API_Controller.php');

class Chart_API_Controller extends Base_API_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Budget_model');
    }

    public function chart()
    {
        $user_id = $this->auth_user();

        $period = $this->input->get('period') ?: 'monthly';

        return $this->json([

            'status'=>true,
            'data'=>$this->Budget_model
                ->get_chart_data(
                    $user_id,
                    $period
                )
        ]);
    }
}