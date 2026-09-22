<?php

defined('BASEPATH') OR exit('No direct script access allowed');

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

        $this->db->insert($this->table, $data);

        if ($data['type'] === 'income') {
            $this->db
                ->set('amount', 'amount + ' . (float) $data['amount'], false)
                ->where('user_id', $data['user_id'])
                ->update('budgets');
        } else {
            $this->db
                ->set('amount', 'amount - ' . (float) $data['amount'], false)
                ->where('user_id', $data['user_id'])
                ->update('budgets');
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function get_user_transactions($user_id)
    {
        return $this->db
            ->select('transactions.*, categories.title as category_name')
            ->from('transactions')
            ->join(
                'categories',
                'categories.id = transactions.category_id',
                'left'
            )
            ->where('transactions.user_id',$user_id)
            ->order_by('transactions.id','DESC')
            ->get()
            ->result();
    }
}