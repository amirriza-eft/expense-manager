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
            ->get($this->table)
            ->result();
    }

    public function create($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function update($id, $data)
    {
        return $this->db
            ->where('id', $id)
            ->update($this->table, $data);
    }
}