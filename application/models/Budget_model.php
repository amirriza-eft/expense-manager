<?php

class Budget_model extends CI_Model
{
    protected $table = 'budgets';

    public function __construct()
    {
        parent::__construct();

        $this->load->database();
    }

    public function get_user_budget($user_id)
    {
        return $this->db
            ->where('user_id', $user_id)
            ->get($this->table)
            ->row();
    }
}