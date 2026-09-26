<?php

defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
    protected $table = 'users';

    public function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    public function create($data)
    {
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    public function create_budget($user_id)
    {
        return $this->db->insert('budgets', [
            'user_id' => $user_id,
            'amount' => 0
        ]);
    }

    public function email_exists($email, $except_user_id = null)
    {
        $this->db
            ->where('email', $email)
            ->where('deleted_at IS NULL', null, false);

        if ($except_user_id !== null) {
            $this->db->where('id !=', $except_user_id);
        }

        return $this->db
                ->count_all_results($this->table) > 0;
    }

    public function find_by_email($email)
    {
        return $this->db
            ->where('email', $email)
            ->where('deleted_at IS NULL', null, false)
            ->get($this->table)
            ->row();
    }

    public function find_any_by_email($email)
    {
        return $this->db
            ->where('email', $email)
            ->get($this->table)
            ->row();
    }

    public function find($id)
    {
        return $this->db
            ->where('id', $id)
            ->where('deleted_at IS NULL', null, false)
            ->get($this->table)
            ->row();
    }

    public function update($id, $data)
    {
        return $this->db
            ->where('id', $id)
            ->update($this->table, $data);
    }

    public function soft_delete($id)
    {
        return $this->db
            ->where('id', $id)
            ->update($this->table, [
                'deleted_at' => date('Y-m-d H:i:s')
            ]);
    }

    public function find_deleted_by_email($email)
    {
        return $this->db
            ->where('email', $email)
            ->where('deleted_at IS NOT NULL', null, false)
            ->get($this->table)
            ->row();
    }

    public function restore($id)
    {
        $this->db->set('deleted_at', null);
        $this->db->set('updated_at', date('Y-m-d H:i:s'));
        $this->db->where('id', $id);
        $this->db->where('deleted_at IS NOT NULL', null, false);

        return $this->db->update($this->table);
    }
}