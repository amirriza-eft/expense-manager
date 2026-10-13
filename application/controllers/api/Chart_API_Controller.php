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

        $from_raw = $this->input->get('from');
        $to_raw = $this->input->get('to');

        if ($from_raw === null || $from_raw === '' || $to_raw === null || $to_raw === '') {
            $range = current_jalali_month_range(false);
            $from = $range['from'];
            $to = $range['month_end'];
        } else {
            $from = parse_gregorian_date($from_raw);
            $to = parse_gregorian_date($to_raw);

            if ($from === null || $to === null) {
                return $this->json([
                    'status' => false,
                    'message' => 'تاریخ وارد شده معتبر نیست',
                ], 400);
            }

            if ($from > $to) {
                return $this->json([
                    'status' => false,
                    'message' => 'تاریخ شروع نمی‌تواند بعد از تاریخ پایان باشد',
                ], 400);
            }
        }

        $empty_after_today = is_current_jalali_month_range($from, $to);

        return $this->json([
            'status' => true,
            'data' => $this->Budget_model->get_chart_data(
                $user_id,
                $from,
                $to,
                $empty_after_today
            ),
        ]);
    }
}
