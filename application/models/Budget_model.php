<?php

class Budget_model extends CI_Model
{
    protected $table = 'budgets';

    public function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    public function get_user_budget($user_id)
    {
        return $this->db
            ->where('user_id', $user_id)
            ->get($this->table)
            ->row();
    }

    public function get_chart_data($user_id, $period = 'monthly')
    {
        $days = ($period === 'weekly') ? 7 : 30;

        $from = date('Y-m-d', strtotime('-' . ($days - 1) . ' days'));
        $to   = date('Y-m-d');

        $budget = $this->db
            ->select('amount')
            ->where('user_id', $user_id)
            ->get('budgets')
            ->row();

        $current_balance = (float) ($budget->amount ?? 0);

        $period_net = $this->db
            ->select("
            COALESCE(
                SUM(
                    IF(type = 'income', amount, -amount)
                ),
                0
            ) AS net
        ", false)
            ->where('user_id', $user_id)
            ->where('deleted_at IS NULL', null, false)
            ->where('transaction_date >=', $from)
            ->where('transaction_date <=', $to . ' 23:59:59')
            ->get('transactions')
            ->row();

        $balance = $current_balance - (float) ($period_net->net ?? 0);

        $query = $this->db
            ->select("DATE(transaction_date) AS day", false)
            ->select("
            SUM(
                IF(type = 'income', amount, 0)
            ) AS income
        ", false)
            ->select("
            SUM(
                IF(type = 'expense', amount, 0)
            ) AS expense
        ", false)
            ->where('user_id', $user_id)
            ->where('deleted_at IS NULL', null, false)
            ->where('transaction_date >=', $from)
            ->where('transaction_date <=', $to . ' 23:59:59')
            ->group_by('DATE(transaction_date)')
            ->order_by('day', 'ASC')
            ->get('transactions')
            ->result();

        $totals = [];

        foreach ($query as $row) {
            $totals[$row->day] = [
                'income'  => (float) $row->income,
                'expense' => (float) $row->expense,
            ];
        }

        $data = [];

        for ($i = 0; $i < $days; $i++) {

            $day = date(
                'Y-m-d',
                strtotime($from . ' +' . $i . ' days')
            );

            $income = $totals[$day]['income'] ?? 0;
            $expense = $totals[$day]['expense'] ?? 0;

            $balance += $income - $expense;

            $data[] = [
                'day'     => $day,
                'income'  => $income,
                'expense' => $expense,
                'balance' => $balance,
            ];
        }

        return $data;
    }
}