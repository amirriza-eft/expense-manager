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
        if ($period === 'weekly') {
            $days = 6;
        } else {
            $days = 30;
        }

        $from_date = date(
            'Y-m-d',
            strtotime("-{$days} days")
        );

        $to_date = date('Y-m-d');

        /*
         * Get daily income and expense.
         */
        $rows = $this->db
            ->select("
            DATE(transactions.transaction_date) AS day,

            SUM(
                CASE
                    WHEN transactions.type = 'income'
                    THEN transactions.amount
                    ELSE 0
                END
            ) AS income,

            SUM(
                CASE
                    WHEN transactions.type = 'expense'
                    THEN transactions.amount
                    ELSE 0
                END
            ) AS expense
        ")
            ->from('transactions')
            ->where('transactions.user_id', $user_id)
            ->where(
                'transactions.deleted_at IS NULL',
                null,
                false
            )
            ->where(
                'transactions.transaction_date >=',
                $from_date
            )
            ->where(
                'transactions.transaction_date <=',
                $to_date
            )
            ->group_by('DATE(transactions.transaction_date)')
            ->order_by('day', 'ASC')
            ->get()
            ->result();

        /*
         * Calculate balance for each day.
         *
         * We need the balance BEFORE the chart period starts,
         * then add each day's income/expense.
         */
        $starting = $this->db
            ->select("
            COALESCE(
                SUM(
                    CASE
                        WHEN type = 'income'
                        THEN amount
                        ELSE -amount
                    END
                ),
                0
            ) AS balance
        ")
            ->from('transactions')
            ->where('user_id', $user_id)
            ->where(
                'deleted_at IS NULL',
                null,
                false
            )
            ->where(
                'transaction_date <',
                $from_date
            )
            ->get()
            ->row();

        $balance = (float) ($starting->balance ?? 0);

        foreach ($rows as $row) {

            $income = (float) $row->income;
            $expense = (float) $row->expense;

            $balance += $income - $expense;

            $row->balance = $balance;
        }

        return $rows;
    }
}