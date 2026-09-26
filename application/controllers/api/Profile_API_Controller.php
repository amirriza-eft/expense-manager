<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'controllers/api/Base_API_Controller.php');

class Profile_API_Controller extends Base_API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('User_model');
        $this->load->library('upload');

        require_once APPPATH . 'policies/User_policy.php';
        $this->User_policy = new User_policy();
    }


    public function get()
    {
        $user_id = $this->session->userdata('user_id');
        $user = $this->User_model->find($user_id);

        if (!$user) {
            return $this->json([
                'status' => false,
                'message' => 'کاربر پیدا نشد'
            ], 404);
        }

        return $this->json([
            'status' => true,
            'user' => $user
        ]);
    }


    public function update()
    {
        $user_id = $this->session->userdata('user_id');

        $data = [
            'full_name' => trim($this->input->post('full_name', true)),
            'email' => trim($this->input->post('email', true)),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if (!$this->User_policy->can_update($user_id, $data)) {
            return $this->json([
                'status' => false,
                'message' => 'نام و ایمیل الزامی هستند و ایمیل باید معتبر باشد'
            ], 422);
        }

        if ($this->User_model->email_exists($data['email'], $user_id)) {
            return $this->json([
                'status' => false,
                'message' => 'این ایمیل قبلاً استفاده شده است'
            ], 422);
        }

        if (!empty($_FILES['avatar']['name'])) {

            $config = [
                'upload_path' => FCPATH . '../uploads/avatars/',
                'allowed_types' => 'jpg|jpeg|png|webp',
                'max_size' => 2048,
                'encrypt_name' => true
            ];

            $this->upload->initialize($config);

            if (!$this->upload->do_upload('avatar')) {
                return $this->json([
                    'status' => false,
                    'message' => $this->upload->display_errors('', '')
                ], 400);
            }

            $upload = $this->upload->data();

            $data['avatar'] = $upload['file_name'];
        }

        if ($this->User_model->update($user_id, $data)) {

            $session_data = [
                'full_name' => $data['full_name'],
                'user_name' => $data['full_name'],
                'email' => $data['email'],
            ];

            if (!empty($data['avatar'])) {
                $session_data['user_avatar'] = $data['avatar'];
            }

            $this->session->set_userdata($session_data);

            $this->session->unset_userdata('avatar_synced');

            $user = $this->User_model->find($user_id);

            return $this->json([
                'status' => true,
                'message' => 'اطلاعات با موفقیت ذخیره شد',
                'user' => [
                    'full_name' => $user->full_name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                ]
            ]);
        }

        return $this->json([
            'status' => false,
            'message' => 'خطا در ذخیره اطلاعات'
        ], 500);
    }

    public function change_password()
    {
        $user_id = $this->session->userdata('user_id');

        $user = $this->User_model->find($user_id);

        $current = $this->input->post('current_password');

        $new = $this->input->post('new_password');

        $confirm = $this->input->post('new_password_confirm');

        if (!password_verify($current, $user->password)) {
            return $this->json([
                'status' => false,
                'message' => 'رمز عبور فعلی اشتباه است'
            ], 400);
        }

        if ($new !== $confirm) {
            return $this->json([
                'status' => false,
                'message' => 'تکرار رمز عبور صحیح نیست'
            ], 400);
        }

        $this->User_model->update(
            $user_id,
            [
                'password' => password_hash(
                    $new,
                    PASSWORD_DEFAULT
                )
            ]
        );

        return $this->json([
            'status' => true,
            'message' => 'رمز عبور تغییر کرد'
        ]);
    }

}