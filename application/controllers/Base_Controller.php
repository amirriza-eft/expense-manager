<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Base_Controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');
        $this->load->model('User_model');

        $this->authCheck();
        $this->syncSessionProfile();
    }

    protected function authCheck()
    {
        if (!$this->session->userdata('user_id')) {
            redirect('login');
            exit;
        }
    }

    /**
     * Keep display fields (name/avatar) in session for navbar and profile UI.
     */
    protected function syncSessionProfile()
    {
        if ($this->session->userdata('avatar_synced')) {
            return;
        }

        $user_id = $this->session->userdata('user_id');
        if (!$user_id) {
            return;
        }

        $user = $this->User_model->find($user_id);
        if (!$user) {
            return;
        }

        $this->session->set_userdata([
            'full_name' => $user->full_name,
            'user_name' => $user->full_name,
            'user_avatar' => $user->avatar ?? null,
            'email' => $user->email,
            'avatar_synced' => true,
        ]);
    }
}
