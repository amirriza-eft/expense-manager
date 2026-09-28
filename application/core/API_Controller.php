<?php

class API_Controller extends CI_Controller
{
    protected function auth_user()
    {
        $user_id = $this->session->userdata('user_id');

        if(!$user_id){

            $this->json([
                'status'=>false,
                'message'=>'ابتدا وارد شوید'
            ],401);

            exit;
        }

        return $user_id;
    }
}