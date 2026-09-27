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

        $result = $this->db->insert($this->table, $data);

        if (!$result) {
            $this->db->trans_rollback();
            return false;
        }

        $budget_changed = $this->change_budget(
            $data['user_id'],
            $data['amount'],
            $data['type'],
            true
        );

        if (!$budget_changed) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function get_user_transactions(
        $user_id,
        $limit,
        $offset,
        $search = null,
        $type = null,
        $category_id = null,
        $from_date = null,
        $to_date = null,
        $sort = 'newest'
    )
    {
        $this->db
            ->select('transactions.*, categories.title as category_name')
            ->from('transactions')
            ->join(
                'categories',
                'categories.id = transactions.category_id',
                'left'
            )
            ->where('transactions.user_id', $user_id)
            ->where('transactions.deleted_at IS NULL', null, false);

        if (!empty($search)) {

            $this->db->group_start()
                ->like('transactions.title', $search)
                ->or_like('transactions.description', $search)
                ->or_like('categories.title', $search)
                ->group_end();

        }

        if (!empty($type)) {
            $this->db->where(
                'transactions.type',
                $type
            );
        }

        if (!empty($category_id)) {
            $this->db->where(
                'transactions.category_id',
                $category_id
            );
        }

        if (!empty($from_date)) {
            $this->db->where(
                'transactions.transaction_date >=',
                $from_date
            );
        }


        if (!empty($to_date)) {
            $this->db->where(
                'transactions.transaction_date <=',
                $to_date
            );
        }

        if ($sort === 'oldest') {
            $this->db
                ->order_by('transactions.transaction_date', 'ASC')
                ->order_by('transactions.id', 'ASC');

        } else {
            $this->db
                ->order_by('transactions.transaction_date', 'DESC')
                ->order_by('transactions.id', 'DESC');

        }
        return $this->db
            ->limit($limit, $offset)
            ->get()
            ->result();
    }

    public function count_user_transactions(
        $user_id,
        $search = null,
        $type = null,
        $category_id = null,
        $from_date = null,
        $to_date = null
    )
    {
        $this->db
            ->from('transactions')
            ->join(
                'categories',
                'categories.id = transactions.category_id',
                'left'
            )
            ->where('transactions.user_id', $user_id)
            ->where('transactions.deleted_at IS NULL', null, false);

        if (!empty($search)) {
            $this->db->group_start()
                ->like('transactions.title', $search)
                ->or_like('transactions.description', $search)
                ->or_like('categories.title', $search)
                ->group_end();
        }

        if (!empty($type)) {
            $this->db->where(
                'transactions.type',
                $type
            );
        }

        if (!empty($category_id)) {
            $this->db->where(
                'transactions.category_id',
                $category_id
            );
        }

        if (!empty($from_date)) {
            $this->db->where(
                'transactions.transaction_date >=',
                $from_date
            );
        }


        if (!empty($to_date)) {
            $this->db->where(
                'transactions.transaction_date <=',
                $to_date . ' 23:59:59'
            );
        }

        return $this->db->count_all_results();
    }

    public function update($id, $user_id, $data)
    {
        $old = $this->db
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->where('deleted_at IS NULL', null, false)
            ->get($this->table)
            ->row();

        if (!$old) {
            return false;
        }

        $this->db->trans_start();

        $old_budget_changed = $this->change_budget(
            $user_id,
            $old->amount,
            $old->type,
            false
        );

        if (!$old_budget_changed) {
            $this->db->trans_rollback();
            return false;
        }

        $new_budget_changed = $this->change_budget(
            $user_id,
            $data['amount'],
            $data['type'],
            true
        );

        if (!$new_budget_changed) {
            $this->db->trans_rollback();
            return false;
        }

        $updated = $this->db
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->where('deleted_at IS NULL', null, false)
            ->update($this->table, $data);

        if (!$updated) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function delete($id, $user_id)
    {
        $transaction = $this->db
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->where('deleted_at IS NULL', null, false)
            ->get($this->table)
            ->row();

        if (!$transaction) {
            return false;
        }

        $this->db->trans_start();

        $budget_changed = $this->change_budget(
            $user_id,
            $transaction->amount,
            $transaction->type,
            false
        );

        if (!$budget_changed) {
            $this->db->trans_rollback();
            return false;
        }

        $deleted = $this->db
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->where('deleted_at IS NULL', null, false)
            ->update($this->table, [
                'deleted_at' => date('Y-m-d H:i:s')
            ]);

        if (!$deleted) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function get_user_deleted_transactions($user_id)
    {
        return $this->db
            ->select("
                transactions.*, 
                categories.title as category_name,
                DATEDIFF(
                    DATE_ADD(transactions.deleted_at, INTERVAL 30 DAY),
                    NOW()
                ) AS days_left
            ")
            ->from('transactions')
            ->join(
                'categories',
                'categories.id = transactions.category_id',
                'left'
            )
            ->where('transactions.user_id', $user_id)
            ->where('transactions.deleted_at IS NOT NULL', null, false)
            ->where(
                'transactions.deleted_at >=',
                date('Y-m-d H:i:s', strtotime('-30 days'))
            )
            ->order_by('transactions.deleted_at', 'DESC')
            ->order_by('transactions.id', 'DESC')
            ->get()
            ->result();
    }

    public function restore($id, $user_id)
    {
        $transaction = $this->db
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->where('deleted_at IS NOT NULL', null, false)
            ->get($this->table)
            ->row();

        if (!$transaction) {
            return false;
        }

        $this->db->trans_start();

        $budget_changed = $this->change_budget(
            $user_id,
            $transaction->amount,
            $transaction->type,
            true
        );

        if (!$budget_changed) {
            $this->db->trans_rollback();
            return false;
        }

        $restored = $this->db
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->where('deleted_at IS NOT NULL', null, false)
            ->update($this->table, [
                'deleted_at' => null
            ]);

        if (!$restored) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
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

    public function recalculate_budget($user_id)
    {
        $income = $this->db
            ->select_sum('amount')
            ->where('user_id', $user_id)
            ->where('type', 'income')
            ->where('deleted_at IS NULL', null, false)
            ->get($this->table)
            ->row()
            ->amount;

        $expense = $this->db
            ->select_sum('amount')
            ->where('user_id', $user_id)
            ->where('type', 'expense')
            ->where('deleted_at IS NULL', null, false)
            ->get($this->table)
            ->row()
            ->amount;

        $income = (float)($income ?? 0);
        $expense = (float)($expense ?? 0);

        $balance = $income - $expense;

        return $this->db
            ->where('user_id', $user_id)
            ->update('budgets', [
                'amount' => $balance
            ]);
    }
}