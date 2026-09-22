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

    public function update($id,$user_id,$data)
    {
        $this->db->trans_start();

        $old=$this->db
            ->where('id',$id)
            ->where('user_id',$user_id)
            ->get($this->table)
            ->row();

        if(!$old)
        {
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
            ->where('id',$id)
            ->where('user_id',$user_id)
            ->update($this->table,$data);

        $this->db->trans_complete();

        return $this->db->trans_status();
    }



    public function delete($id,$user_id)
    {
        $this->db->trans_start();

        $transaction=$this->db
            ->where('id',$id)
            ->where('user_id',$user_id)
            ->get($this->table)
            ->row();

        if(!$transaction)
        {
            return false;
        }

        $this->change_budget(
            $user_id,
            $transaction->amount,
            $transaction->type,
            false
        );

        $this->db
            ->where('id',$id)
            ->where('user_id',$user_id)
            ->delete($this->table);

        $this->db->trans_complete();

        return $this->db->trans_status();
    }



    private function change_budget($user_id,$amount,$type,$add=true)
    {
        $value=(float)$amount;

        if($type==='income')
        {
            $add
                ?
                $this->db->set('amount','amount + '.$value,false)
                :
                $this->db->set('amount','amount - '.$value,false);
        }
        else
        {
            $add
                ?
                $this->db->set('amount','amount - '.$value,false)
                :
                $this->db->set('amount','amount + '.$value,false);
        }

        return $this->db
            ->where('user_id',$user_id)
            ->update('budgets');
    }
}