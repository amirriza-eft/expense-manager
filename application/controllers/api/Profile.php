<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
    }


    public function get()
    {
        $user_id = $this->session->userdata('user_id');
        $user = $this->User_model->find($user_id);

        if(!$user)
        {
            return $this->json([
                'status'=>false,
                'message'=>'کاربر پیدا نشد'
            ],404);
        }

        return $this->json([
            'status'=>true,
            'user'=>$user
        ]);
    }



    public function update()
    {
        $user_id = $this->session->userdata('user_id');

        $data=[
            'full_name'=>$this->input->post('full_name',true),
            'email'=>$this->input->post('email',true),
            'updated_at'=>date('Y-m-d H:i:s')
        ];

        if($this->User_model->update($user_id,$data))
        {
            $this->session->set_userdata([
                'full_name'=>$data['full_name'],
                'email'=>$data['email']
            ]);

            return $this->json([
                'status'=>true,
                'message'=>'اطلاعات با موفقیت ذخیره شد'
            ]);
        }

        return $this->json([
            'status'=>false,
            'message'=>'خطا در ذخیره اطلاعات'
        ],500);
    }



    public function change_password()
    {
        $user_id=$this->session->userdata('user_id');

        $user=$this->User_model->find($user_id);

        $current=$this->input->post('current_password');

        $new=$this->input->post('new_password');

        $confirm=$this->input->post('new_password_confirm');

        if(!password_verify($current,$user->password))
        {
            return $this->json([
                'status'=>false,
                'message'=>'رمز عبور فعلی اشتباه است'
            ],400);
        }

        if($new !== $confirm)
        {
            return $this->json([
                'status'=>false,
                'message'=>'تکرار رمز عبور صحیح نیست'
            ],400);
        }

        $this->User_model->update(
            $user_id,
            [
                'password'=>password_hash(
                    $new,
                    PASSWORD_DEFAULT
                )
            ]
        );

        return $this->json([
            'status'=>true,
            'message'=>'رمز عبور تغییر کرد'
        ]);
    }



    private function json($data,$code=200)
    {
        http_response_code($code);

        header('Content-Type: application/json');

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE
        );

        exit;
    }

}