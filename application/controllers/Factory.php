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

        $count = max(1, (int) $count);

        $start = Carbon::now()
            ->subDays(29)
            ->startOfDay();

        $end = Carbon::now()
            ->endOfDay();

        $this->db
            ->where('user_id', $user_id)
            ->where('description', 'Random generated transaction')
            ->delete('transactions');

        $transactions = [];

        for ($i = 0; $i < $count; $i++) {

            $type = random_int(0, 1) === 0
                ? 'income'
                : 'expense';

            $amount = random_int(100000, 5000000);

            $transaction_date = Carbon::createFromTimestamp(
                random_int(
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
                'transaction_date' => $transaction_date->format(
                    'Y-m-d H:i:s'
                ),
                'created_at' => Carbon::now()->format(
                    'Y-m-d H:i:s'
                ),
                'updated_at' => Carbon::now()->format(
                    'Y-m-d H:i:s'
                ),
            ];
        }

        $this->db->trans_start();

        $this->db->insert_batch(
            'transactions',
            $transactions
        );

        $this->db->trans_complete();

        if (!$this->db->trans_status()) {
            show_error('Failed to generate transactions.');
        }

        if (!$this->Transaction_model->recalculate_budget($user_id)) {
            show_error('Failed to recalculate budget.');
        }

        echo "Generated {$count} transactions for the last 30 days.";
    }
}