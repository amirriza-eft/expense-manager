<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Carbon\Carbon;

class Factory extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->database();
        $this->load->model('Transaction_model');
    }

    public function test()
    {
        echo Carbon::now()->format('Y-m-d H:i:s');
    }

    public function transactions($count = 50)
    {
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            show_error('You must be logged in.');
        }

        $transactions = [];

        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        for ($i = 0; $i < $count; $i++) {

            $type = rand(0, 1) === 0
                ? 'income'
                : 'expense';

            $amount = rand(100000, 5000000);

            $transaction_date = Carbon::createFromTimestamp(
                rand(
                    $start->timestamp,
                    $end->timestamp
                )
            );

            $transactions[] = [
                'user_id' => $user_id,
                'title' => ucfirst($type) . ' transaction',
                'description' => 'Random generated transaction',
                'amount' => $amount,
                'type' => $type,
                'category_id' => null,
                'transaction_date' => $transaction_date->format('Y-m-d H:i:s'),
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
            ];
        }

        $this->db->insert_batch(
            'transactions',
            $transactions
        );

        $this->Transaction_model->recalculate_budget($user_id);

        echo "Generated {$count} transactions successfully.";
    }
}