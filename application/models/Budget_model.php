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
    public function get_chart_data($user_id, $from, $to, $empty_after_today = false)
    {
        $today = app_today();

        $budget = $this->db
            ->select('amount')
            ->where('user_id', $user_id)
            ->get('budgets')
            ->row();

        $current_balance = (float) ($budget->amount ?? 0);

        $balance_to = ($to < $today) ? $to : $today;

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
            ->where('transaction_date <=', $balance_to)
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
            ->where('transaction_date <=', $to)
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

        foreach (each_date_inclusive($from, $to) as $day) {
            $is_future = $empty_after_today && $day > $today;

            if ($is_future) {
                $data[] = [
                    'day'     => $day,
                    'label'   => format_jalali_date($day, true),
                    'income'  => null,
                    'expense' => null,
                    'balance' => null,
                ];
                continue;
            }

            $income = $totals[$day]['income'] ?? 0;
            $expense = $totals[$day]['expense'] ?? 0;

            $balance += $income - $expense;

            $data[] = [
                'day'     => $day,
                'label'   => format_jalali_date($day, true),
                'income'  => $income,
                'expense' => $expense,
                'balance' => $balance,
            ];
        }

        return $data;
    }
}
