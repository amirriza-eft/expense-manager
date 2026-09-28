<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Profile_service
{
    protected $user_model;
    protected $user_policy;
    protected $upload;
    protected $session;

    public function __construct($user_model, $user_policy, $upload, $session)
    {
        $this->user_model = $user_model;
        $this->user_policy = $user_policy;
        $this->upload = $upload;
        $this->session = $session;
    }

    public function get($user_id)
    {
        $user = $this->user_model->find($user_id);

        if (!$user) {
            return [
                'status' => false,
                'message' => 'کاربر پیدا نشد'
            ];
        }

        return [
            'status' => true,
            'user'=>[
                'id'=>$user->id,
                'full_name'=>$user->full_name,
                'email'=>$user->email,
                'avatar'=>$user->avatar
            ]
        ];
    }

    public function update($user_id, $input, $files)
    {
        $data = [
            'full_name' => trim($input['full_name'] ?? ''),
            'email' => trim($input['email'] ?? ''),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if (!$this->user_policy->can_update($user_id, $data)) {
            return [
                'status' => false,
                'message' => 'نام و ایمیل الزامی هستند و ایمیل باید معتبر باشد',
                'code' => 422
            ];
        }

        if ($this->user_model->email_exists($data['email'], $user_id)) {
            return [
                'status' => false,
                'message' => 'این ایمیل قبلاً استفاده شده است',
                'code' => 422
            ];
        }

        if (!empty($files['avatar']['name'])) {

            $this->upload->initialize([
                'upload_path' => FCPATH . '../uploads/avatars/',
                'allowed_types' => 'jpg|jpeg|png|webp',
                'max_size' => 2048,
                'encrypt_name' => true
            ]);

            if (!$this->upload->do_upload('avatar')) {
                return [
                    'status' => false,
                    'message' => $this->upload->display_errors('', ''),
                    'code' => 400
                ];
            }

            $upload = $this->upload->data();
            $data['avatar'] = $upload['file_name'];
        }

        if (!$this->user_model->update($user_id, $data)) {
            return [
                'status' => false,
                'message' => 'خطا در ذخیره اطلاعات',
                'code' => 500
            ];
        }

        $session = [
            'full_name' => $data['full_name'],
            'user_name' => $data['full_name'],
            'email' => $data['email']
        ];

        if (!empty($data['avatar'])) {
            $session['user_avatar'] = $data['avatar'];
        }

        $this->session->set_userdata($session);

        $user = $this->user_model->find($user_id);

        return [
            'status' => true,
            'message' => 'اطلاعات با موفقیت ذخیره شد',
            'user' => [
                'full_name' => $user->full_name,
                'email' => $user->email,
                'avatar' => $user->avatar
            ]
        ];
    }


    public function change_password($user_id, $input)
    {
        $user = $this->user_model->find($user_id);

        if (!password_verify($input['current_password'], $user->password)) {
            return [
                'status' => false,
                'message' => 'رمز عبور فعلی اشتباه است',
                'code' => 400
            ];
        }

        if ($input['new_password'] !== $input['new_password_confirm']) {
            return [
                'status' => false,
                'message' => 'تکرار رمز عبور صحیح نیست',
                'code' => 400
            ];
        }

        $this->user_model->update($user_id, [
            'password' => password_hash(
                $input['new_password'],
                PASSWORD_DEFAULT
            )
        ]);

        return [
            'status' => true,
            'message' => 'رمز عبور تغییر کرد'
        ];
    }
}