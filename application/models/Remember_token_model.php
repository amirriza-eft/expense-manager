<?php defined('BASEPATH') or exit('No direct script access allowed');

class Remember_token_model extends CI_Model
{
    protected $table = 'remember_tokens';

    public function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    public function create($data)
    {
        return $this->db
            ->insert($this->table, $data);
    }

    public function find_by_selector($selector)
    {
        return $this->db
            ->where('selector', $selector)
            ->get($this->table)
            ->row();
    }

    public function delete_by_selector($selector)
    {
        return $this->db
            ->where('selector', $selector)
            ->delete($this->table);
    }

    public function delete_by_user($user_id)
    {
        return $this->db
            ->where('user_id', $user_id)
            ->delete($this->table);
    }

    public function delete_expired()
    {
        return $this->db
            ->where('expires_at <', date('Y-m-d H:i:s'))
            ->delete($this->table);
    }
}