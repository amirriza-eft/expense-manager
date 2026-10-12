<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Category_model extends CI_Model
{
    protected $table = 'categories';

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_user_categories($user_id)
    {
        return $this->db
            ->where('user_id', $user_id)
            ->where('deleted_at IS NULL', null, false)
            ->order_by('title', 'ASC')
            ->get($this->table)
            ->result();
    }

    public function get_user_deleted_categories($user_id)
    {
        return $this->db
            ->where('user_id', $user_id)
            ->where('deleted_at IS NOT NULL', null, false)
            ->order_by('deleted_at', 'DESC')
            ->get($this->table)
            ->result();
    }

    public function get_by_id($id,$user_id)
    {
        return $this->db
            ->where('id',$id)
            ->where('user_id',$user_id)
            ->get($this->table)
            ->row();
    }

    public function get_active_by_id($id, $user_id)
    {
        return $this->db
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->where('deleted_at IS NULL', null, false)
            ->get($this->table)
            ->row();
    }

    public function create($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $user_id, $data)
    {
        return $this->db
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->where('deleted_at IS NULL', null, false)
            ->update($this->table, $data);
    }

    public function delete($id, $user_id)
    {
        $this->db->trans_start();

        $category_deleted = $this->db
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->where('deleted_at IS NULL', null, false)
            ->update($this->table, [
                'deleted_at' => date('Y-m-d H:i:s')
            ]);

        if (!$category_deleted) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db
            ->where('category_id', $id)
            ->where('user_id', $user_id)
            ->where('deleted_at IS NULL', null, false)
            ->update('transactions', [
                'deleted_at' => date('Y-m-d H:i:s')
            ]);

        $this->load->model('Transaction_model');

        if (!$this->Transaction_model->recalculate_budget($user_id)) {
            $this->db->trans_rollback();
            return false;
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }

    public function restore($id, $user_id)
    {
        return $this->db
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->where('deleted_at IS NOT NULL', null, false)
            ->update($this->table, [
                'deleted_at' => null
            ]);
    }
}
