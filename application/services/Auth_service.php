<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Auth_service
{
    protected $user_model;
    protected $session;

    public function __construct($user_model,$session)
    {
        $this->user_model = $user_model;
        $this->session = $session;
    }

    public function register($data)
    {
        if( $this->user_model->find_any_by_email($data['email']) )
        {
            return [
                'status'=>false,
                'message'=>'این ایمیل قبلا ثبت شده است'
            ];
        }

        $data['password'] =
            password_hash(
                $data['password'],
                PASSWORD_DEFAULT
            );


        $data['created_at'] =
            date('Y-m-d H:i:s');


        $user_id = $this->user_model->create($data);

        if(!$user_id){
            return [
                'status'=>false,
                'message'=>'خطا در ایجاد حساب'
            ];
        }

        $this->user_model->create_budget($user_id);

        return [
            'status'=>true,
            'message'=>'ثبت نام موفق'
        ];
    }

    public function login($email, $password)
    {
        $user = $this->user_model->find_by_email($email);

        if ($user) {

            if (!password_verify($password, $user->password)) {

                return [
                    'status' => false,
                    'message' => 'ایمیل یا رمز عبور اشتباه است'
                ];

            }

            $this->session->set_userdata([

                'user_id' => $user->id,
                'full_name' => $user->full_name,
                'user_name' => $user->full_name,
                'user_avatar' => $user->avatar ?? null,
                'email' => $user->email,
                'logged_in' => true

            ]);

            return [
                'status' => true,
                'message' => 'ورود موفق'
            ];

        }

        $deleted = $this->user_model->find_deleted_by_email($email);

        if ( $deleted && password_verify($password, $deleted->password) )
        {
            return [
                'status' => false,
                'code' => 'account_deleted',
                'message' =>
                    'این حساب حذف شده است. برای بازیابی حساب کلیک کنید.'
            ];
        }

        return [
            'status' => false,
            'message' => 'ایمیل یا رمز عبور اشتباه است'
        ];

    }


    public function restore($email,$password)
    {
        $user = $this->user_model->find_deleted_by_email($email);

        if( !$user || !password_verify($password,$user->password) )
        {
            return [
                'status'=>false,
                'message'=>'ایمیل یا رمز عبور اشتباه است'
            ];
        }

        if( $this->user_model->restore($user->id) )
        {
            return [
                'status'=>true,
                'message'=>'حساب شما بازیابی شد'
            ];
        }

        return [
            'status'=>false,
            'message'=>'خطا در بازیابی حساب'
        ];
    }

    public function delete($user_id,$password)
    {

        $user = $this->user_model->find($user_id);

        if( !$user || !password_verify($password,$user->password) )
        {
            return [
                'status'=>false,
                'message'=>'رمز عبور اشتباه است'
            ];
        }

        if( $this->user_model->soft_delete($user_id) )
        {
            $this->session->sess_destroy();

            return [
                'status'=>true,
                'message'=>'حساب شما حذف شد'
            ];
        }

        return [
            'status'=>false,
            'message'=>'خطا در حذف حساب'
        ];
    }
}