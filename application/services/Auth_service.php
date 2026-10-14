<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Auth_service
{
    protected $user_model;
    protected $session;
    protected $remember_token_model;

    public function __construct($user_model, $session, $remember_token_model)
    {
        $this->user_model = $user_model;
        $this->session = $session;
        $this->remember_token_model = $remember_token_model;
    }

    public function security_questions(): array
    {
        $questions = [];

        foreach (security_questions_list() as $id => $text) {
            $questions[] = ['id' => $id, 'text' => $text];
        }

        return $questions;
    }

    public function register($data)
    {
        $data['full_name'] = trim($data['full_name'] ?? '');
        $data['email'] = trim($data['email'] ?? '');

        $question_id = (int) ($data['security_question_id'] ?? 0);
        $security_answer = $data['security_answer'] ?? '';

        unset($data['security_answer']);

        if (!is_valid_security_question_id($question_id)) {
            return [
                'status' => false,
                'message' => 'سوال امنیتی انتخاب‌شده معتبر نیست'
            ];
        }

        if (normalize_security_answer($security_answer) === '') {
            return [
                'status' => false,
                'message' => 'پاسخ سوال امنیتی الزامی است'
            ];
        }

        if( $this->user_model->find_any_by_email($data['email']) )
        {
            return [
                'status'=>false,
                'message'=>'این ایمیل قبلا ثبت شده است'
            ];
        }

        if ($this->user_model->full_name_exists($data['full_name'])) {
            return [
                'status' => false,
                'message' => 'این نام و نام خانوادگی قبلا ثبت شده است'
            ];
        }

        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['security_question_id'] = $question_id;
        $data['security_answer_hash'] = hash_security_answer($security_answer);

        $data['created_at'] = date('Y-m-d H:i:s');

        $db = $this->user_model->db;
        $db->trans_start();

        $user_id = $this->user_model->create($data);

        if (!$user_id) {
            $db->trans_rollback();

            return [
                'status' => false,
                'message' => 'خطا در ایجاد حساب'
            ];
        }

        if (!$this->user_model->create_budget($user_id)) {
            $db->trans_rollback();

            return [
                'status' => false,
                'message' => 'خطا در ایجاد حساب'
            ];
        }

        $db->trans_complete();

        if (!$db->trans_status()) {
            return [
                'status' => false,
                'message' => 'خطا در ایجاد حساب'
            ];
        }

        return [
            'status' => true,
            'message' => 'ثبت نام موفق'
        ];
    }

    public function login($email, $password, $remember_me = false)
    {
        $user = $this->user_model->find_by_email($email);

        if (!$user) {

            $deleted = $this->user_model->find_deleted_by_email($email);

            if ($deleted && password_verify($password, $deleted->password)) {
                return [
                    'status' => false,
                    'code' => 'account_deleted',
                    'message' => 'این حساب حذف شده است. برای بازیابی حساب کلیک کنید.'
                ];
            }

            return [
                'status' => false,
                'message' => 'ایمیل یا رمز عبور اشتباه است'
            ];
        }

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

        $db = $this->user_model->db;
        $db->trans_start();

        if (!$this->user_model->soft_delete($user_id)) {
            $db->trans_rollback();

            return [
                'status' => false,
                'message' => 'خطا در حذف حساب'
            ];
        }

        $this->remember_token_model->delete_by_user($user_id);

        $db->trans_complete();

        if (!$db->trans_status()) {
            return [
                'status' => false,
                'message' => 'خطا در حذف حساب'
            ];
        }

        $this->session->sess_destroy();

        return [
            'status' => true,
            'message' => 'حساب شما حذف شد'
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

        $token = $this->remember_token_model->find_by_selector($selector);

        if (!$token) {
            $this->forget_remember_cookie();
            return false;
        }

        if (strtotime($token->expires_at) < time())
        {
            $this->remember_token_model->delete_by_selector($selector);
            $this->forget_remember_cookie();
            return false;
        }

        if (!hash_equals($token->token_hash, hash('sha256', $validator)))
        {
            $this->remember_token_model->delete_by_selector($selector);
            $this->forget_remember_cookie();
            return false;
        }

        $user = $this->user_model->find($token->user_id);

        if (!$user) {
            $this->remember_token_model->delete_by_selector($selector);
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
                'expires' => 1,
                'path' => '/',
                'secure' => !empty($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]
        );

        unset($_COOKIE['remember_me']);
    }

    public function logout()
    {
        $user_id = $this->session->userdata('user_id');

        if ($user_id) {
            $this->remember_token_model->delete_by_user($user_id);
        }

        $this->clear_password_reset();
        $this->forget_remember_cookie();

        $this->session->sess_destroy();
    }

    public function recovery_get_question($email)
    {
        $email = trim((string) $email);
        $user = $this->user_model->find_by_email($email);

        if (!$user || empty($user->security_question_id) || empty($user->security_answer_hash))
        {
            return [
                'status' => false,
                'message' => 'بازیابی با سوال امنیتی برای این حساب در دسترس نیست. اگر حساب دارید، از صفحه پروفایل سوال امنیتی را تنظیم کنید.'
            ];
        }

        $question = security_question_text($user->security_question_id);

        if ($question === '') {
            return [
                'status' => false,
                'message' => 'بازیابی با سوال امنیتی برای این حساب در دسترس نیست.'
            ];
        }

        return [
            'status' => true,
            'question' => $question
        ];
    }

    public function recovery_verify_answer($email, $answer)
    {
        $email = trim((string) $email);
        $user = $this->user_model->find_by_email($email);

        if (!$user || empty($user->security_question_id) || empty($user->security_answer_hash))
        {
            return [
                'status' => false,
                'message' => 'پاسخ اشتباه است'
            ];
        }

        if (!verify_security_answer($answer, $user->security_answer_hash)) {
            return [
                'status' => false,
                'message' => 'پاسخ اشتباه است'
            ];
        }

        $this->session->set_userdata([
            'password_reset_user_id' => (int) $user->id,
            'password_reset_expires' => time() + (15 * 60)
        ]);

        return [
            'status' => true,
            'message' => 'پاسخ صحیح است. رمز عبور جدید را وارد کنید.'
        ];
    }

    public function recovery_reset_password($new_password, $new_password_confirm)
    {
        $user_id = (int) $this->session->userdata('password_reset_user_id');
        $expires = (int) $this->session->userdata('password_reset_expires');

        if (!$user_id || !$expires || time() > $expires) {
            $this->clear_password_reset();

            return [
                'status' => false,
                'message' => 'اجازه بازیابی منقضی شده است. دوباره از ابتدا شروع کنید.'
            ];
        }

        if ($new_password !== $new_password_confirm) {
            return [
                'status' => false,
                'message' => 'تکرار رمز عبور صحیح نیست'
            ];
        }

        if (strlen($new_password) < 8) {
            return [
                'status' => false,
                'message' => 'رمز عبور باید حداقل ۸ کاراکتر باشد'
            ];
        }

        $user = $this->user_model->find($user_id);

        if (!$user) {
            $this->clear_password_reset();

            return [
                'status' => false,
                'message' => 'کاربر پیدا نشد'
            ];
        }

        $updated = $this->user_model->update($user_id, [
            'password' => password_hash($new_password, PASSWORD_DEFAULT)
        ]);

        $this->clear_password_reset();

        if (!$updated) {
            return [
                'status' => false,
                'message' => 'خطا در تغییر رمز عبور'
            ];
        }

        return [
            'status' => true,
            'message' => 'رمز عبور با موفقیت تغییر کرد'
        ];
    }

    private function clear_password_reset()
    {
        $this->session->unset_userdata([
            'password_reset_user_id',
            'password_reset_expires'
        ]);
    }
}