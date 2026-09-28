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
        return $this->db
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->where('deleted_at IS NULL', null, false)
            ->update($this->table, [
                'deleted_at' => date('Y-m-d H:i:s')
            ]);
    }

    public function restore($id, $user_id)
    {
        $this->db->set('deleted_at', null);
        $this->db->where('id', $id);
        $this->db->where('user_id', $user_id);
        $this->db->where('deleted_at IS NOT NULL', null, false);

        return $this->db->update($this->table);
    }
}
