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
}