<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Transaction_model extends CI_Model
{
    protected $table = 'transactions';

    public function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    public function create($data)
    {
        $this->db->trans_start();

        if (!$this->db->insert($this->table, $data)) {
            return false;
        }

        $this->change_budget(
            $data['user_id'],
            $data['amount'],
            $data['type'],
            true
        );

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function get_user_transactions($user_id, $limit, $offset)
    {
        return $this->db
            ->select('transactions.*, categories.title as category_name')
            ->from('transactions')
            ->join(
                'categories',
                'categories.id = transactions.category_id',
                'left'
            )
            ->where('transactions.user_id', $user_id)
            ->where('transactions.deleted_at IS NULL', null, false)
            ->order_by('transactions.id', 'DESC')
            ->limit($limit, $offset)
            ->get()
            ->result();
    }

    public function count_user_transactions($user_id)
    {
        return $this->db
            ->where('user_id', $user_id)
            ->where('deleted_at IS NULL', null, false)
            ->count_all_results('transactions');
    }

    public function update($id, $user_id, $data)
    {
        $this->db->trans_start();

        $old = $this->db
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->get($this->table)
            ->row();

        if (!$old) {
            return false;
        }

        $this->change_budget(
            $user_id,
            $old->amount,
            $old->type,
            false
        );

        $this->change_budget(
            $user_id,
            $data['amount'],
            $data['type'],
            true
        );

        $this->db
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->update($this->table, $data);

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function delete($id, $user_id)
    {
        $transaction = $this->db
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->get($this->table)
            ->row();

        if (!$transaction) {
            return false;
        }

        $this->change_budget(
            $user_id,
            $transaction->amount,
            $transaction->type,
            false
        );

        return $this->db
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->update($this->table, [
                'deleted_at' => date('Y-m-d H:i:s')
            ]);
    }

    private function change_budget($user_id, $amount, $type, $add = true)
    {
        $value = $amount;

        if ($type === 'income') {
            $add
                ?
                $this->db->set('amount', 'amount + ' . $value, false)
                :
                $this->db->set('amount', 'amount - ' . $value, false);
        } else {
            $add
                ?
                $this->db->set('amount', 'amount - ' . $value, false)
                :
                $this->db->set('amount', 'amount + ' . $value, false);
        }

        return $this->db
            ->where('user_id', $user_id)
            ->update('budgets');
    }

    public function get_monthly_income($user_id)
    {
        return $this->db
            ->select_sum('amount')
            ->where('user_id', $user_id)
            ->where('type', 'income')
            ->where('deleted_at IS NULL', null, false)
            ->where('MONTH(transaction_date)', date('m'))
            ->where('YEAR(transaction_date)', date('Y'))
            ->get($this->table)
            ->row()
            ->amount ?? 0;
    }

    public function get_monthly_expense($user_id)
    {
        return $this->db
            ->select_sum('amount')
            ->where('user_id', $user_id)
            ->where('type', 'expense')
            ->where('deleted_at IS NULL', null, false)
            ->where('MONTH(transaction_date)', date('m'))
            ->where('YEAR(transaction_date)', date('Y'))
            ->get($this->table)
            ->row()
            ->amount ?? 0;
    }
}