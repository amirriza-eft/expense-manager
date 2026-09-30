<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Auth_service
{
    protected $user_model;
    protected $session;
    protected $remember_token_model;

    public function __construct(
        $user_model,
        $session,
        $remember_token_model
    ) {
        $this->user_model = $user_model;
        $this->session = $session;
        $this->remember_token_model = $remember_token_model;
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

    public function login($email, $password, $remember_me = false)
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

            if ($remember_me) {
                $this->create_remember_token($user->id);
            }

            return [
                'status' => true,
                'message' => 'ورود موفق'
            ];
        }

        $deleted = $this->user_model->find_deleted_by_email($email);

        if ($deleted && password_verify($password, $deleted->password)) {

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

        if ($this->user_model->soft_delete($user_id))
        {
            $this->remember_token_model->delete_by_user($user_id);

            $this->session->sess_destroy();

            return [
                'status' => true,
                'message' => 'حساب شما حذف شد'
            ];
        }

        return [
            'status'=>false,
            'message'=>'خطا در حذف حساب'
        ];
    }


    private function create_remember_token($user_id)
    {
        $selector = bin2hex(random_bytes(16));
        $validator = bin2hex(random_bytes(32));

        $token_hash = hash('sha256', $validator);

        $expires_at = date(
            'Y-m-d H:i:s',
            time() + (60 * 60 * 24 * 30)
        );

        $this->remember_token_model->create([
            'user_id' => $user_id,
            'selector' => $selector,
            'token_hash' => $token_hash,
            'expires_at' => $expires_at,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $cookie_value = $selector . ':' . $validator;

        setcookie(
            'remember_me',
            $cookie_value,
            [
                'expires' => time() + (60 * 60 * 24 * 30),
                'path' => '/',
                'secure' => !empty($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );
    }


    public function restore_remembered_session()
    {
        if ($this->session->userdata('logged_in')) {
            return true;
        }

        if (empty($_COOKIE['remember_me'])) {
            return false;
        }

        $parts = explode(':', $_COOKIE['remember_me'], 2);

        if (count($parts) !== 2) {
            $this->forget_remember_cookie();
            return false;
        }

        $selector = $parts[0];
        $validator = $parts[1];

        $token = $this->remember_token_model
            ->find_by_selector($selector);

        if (!$token) {
            $this->forget_remember_cookie();
            return false;
        }

        if (strtotime($token->expires_at) < time()) {
            $this->remember_token_model
                ->delete_by_selector($selector);

            $this->forget_remember_cookie();

            return false;
        }

        if (!hash_equals(
            $token->token_hash,
            hash('sha256', $validator)
        )) {
            $this->remember_token_model
                ->delete_by_selector($selector);

            $this->forget_remember_cookie();

            return false;
        }

        $user = $this->user_model->find_by_id($token->user_id);

        if (!$user) {
            $this->remember_token_model
                ->delete_by_selector($selector);

            $this->forget_remember_cookie();

            return false;
        }

        $this->session->set_userdata([
            'user_id' => $user->id,
            'full_name' => $user->full_name,
            'user_name' => $user->full_name,
            'user_avatar' => $user->avatar ?? null,
            'email' => $user->email,
            'logged_in' => true
        ]);

        return true;
    }

    private function forget_remember_cookie()
    {
        setcookie(
            'remember_me',
            '',
            [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => !empty($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );
    }


    public function logout()
    {
        if (!empty($_COOKIE['remember_me'])) {

            $parts = explode(':', $_COOKIE['remember_me'], 2);

            if (count($parts) === 2) {
                $this->remember_token_model
                    ->delete_by_selector($parts[0]);
            }
        }

        setcookie(
            'remember_me',
            '',
            [
                'expires' => time() - 3600,
                'path' => '/',
                'secure' => !empty($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );

        $this->session->sess_destroy();
    }
}