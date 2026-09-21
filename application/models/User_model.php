<?php

defined('BASEPATH') OR exit('No direct script access allowed');

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
        return $this->db->insert($this->table, $data);
    }

    public function find_by_email($email)
    {
        return $this->db
            ->where('email', $email)
            ->where('deleted_at IS NULL', null, false)
            ->get($this->table)
            ->row();
    }

    public function find($id)
    {
        return $this->db
            ->where('id',$id)
            ->where('deleted_at IS NULL',null,false)
            ->get($this->table)
            ->row();
    }

    public function update($id,$data)
    {
        return $this->db
            ->where('id',$id)
            ->update($this->table,$data);
    }
}