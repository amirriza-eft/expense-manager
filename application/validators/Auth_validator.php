<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Auth_validator
{
    protected $form_validation;

    public function __construct($form_validation)
    {
        $this->form_validation = $form_validation;
    }

    public function register()
    {

        $this->form_validation->set_rules(
            'full_name',
            'نام و نام خانوادگی',
            'required|min_length[3]|max_length[100]',
            [
                'required'=>'وارد کردن {field} الزامی است.',
                'min_length'=>'{field} باید حداقل ۳ کاراکتر باشد.',
                'max_length'=>'{field} نباید بیشتر از ۱۰۰ کاراکتر باشد.'
            ]
        );

        $this->form_validation->set_rules(
            'email',
            'ایمیل',
            'required|valid_email|max_length[100]',
            [
                'required'=>'وارد کردن {field} الزامی است.',
                'valid_email'=>'فرمت {field} صحیح نیست.',
                'max_length' => '{field} نباید بیشتر از ۱۰۰ کاراکتر باشد.'
            ]
        );

        $this->form_validation->set_rules(
            'password',
            'رمز عبور',
            'required|min_length[8]|max_length[100]'
        );

        $this->form_validation->set_rules(
            'password_confirm',
            'تکرار رمز عبور',
            'required|matches[password]'
        );

        $this->form_validation->set_rules(
            'security_question_id',
            'سوال امنیتی',
            [
                'required',
                [
                    'valid_security_question',
                    function ($id) {
                        return is_valid_security_question_id($id);
                    }
                ]
            ],
            [
                'required' => 'انتخاب سوال امنیتی الزامی است',
                'valid_security_question' => 'سوال امنیتی انتخاب‌شده معتبر نیست'
            ]
        );

        $this->form_validation->set_rules(
            'security_answer',
            'پاسخ امنیتی',
            [
                'required',
                [
                    'security_answer_ok',
                    function ($answer) {
                        return normalize_security_answer($answer) !== '';
                    }
                ]
            ],
            [
                'required' => 'پاسخ سوال امنیتی الزامی است',
                'security_answer_ok' => 'پاسخ سوال امنیتی نمی‌تواند خالی باشد'
            ]
        );

        return $this->form_validation->run();
    }

    public function recovery_reset()
    {
        $this->form_validation->set_rules(
            'new_password',
            'رمز عبور جدید',
            'required|min_length[8]|max_length[100]'
        );

        $this->form_validation->set_rules(
            'new_password_confirm',
            'تکرار رمز عبور جدید',
            'required|matches[new_password]'
        );

        return $this->form_validation->run();
    }
}
