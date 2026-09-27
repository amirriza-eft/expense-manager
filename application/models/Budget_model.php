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

    public function get_monthly_chart_data($user_id)
    {
        $this->db
            ->select("
            DAY(transactions.transaction_date) as day,
            SUM(CASE WHEN transactions.type = 'income' THEN transactions.amount ELSE 0 END) as income,
            SUM(CASE WHEN transactions.type = 'expense' THEN transactions.amount ELSE 0 END) as expense
        ")
            ->from('transactions')
            ->where('transactions.user_id', $user_id)
            ->where('transactions.deleted_at IS NULL', null, false)
            ->where('MONTH(transaction_date)', date('m'))
            ->where('YEAR(transaction_date)', date('Y'))
            ->group_by('DAY(transaction_date)')
            ->order_by('day', 'ASC');

        return $this->db->get()->result();
    }
}