<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'controllers/api/Base_API_Controller.php');

class Category extends Base_API_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('Category_model');
        $this->load->library('session');

        require_once APPPATH . 'policies/Category_policy.php';
        $this->category_policy = new Category_policy();
    }

    public function index()
    {
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            return $this->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $categories = $this->Category_model
            ->get_user_categories($user_id);

        return $this->json([
            'status' => true,
            'categories' => $categories
        ]);
    }

    public function deleted()
    {
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            return $this->json([
                'status' => false,
                'message' => 'ابتدا وارد حساب خود شوید'
            ], 401);
        }

        $categories = $this->Category_model
            ->get_user_deleted_categories($user_id);

        return $this->json([
            'status' => true,
            'categories' => $categories
        ]);
    }

    public function create()
    {
        $user_id = $this->session->userdata('user_id');

        if (!$this->category_policy->can_create($user_id)) {
            return $this->json([
                'status' => false,
                'message' => 'ابتدا وارد حساب خود شوید'
            ], 401);
        }

        $data = [
            'user_id' => $user_id,
            'title' => $this->input->post('title', true)
        ];

        if ($this->Category_model->create($data)) {
            return $this->json([
                'status' => true,
                'message' => 'دسته‌بندی با موفقیت ایجاد شد'
            ]);
        }

        return $this->json([
            'status' => false,
            'message' => 'خطا در ایجاد دسته‌بندی'
        ], 500);
    }

    public function update($id)
    {
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            return $this->json([
                'status' => false,
                'message' => 'ابتدا وارد حساب خود شوید'
            ], 401);
        }

        $cat = $this->db->get_where('categories', ['id' => $id])->row();

        if (!$this->category_policy->can_update($user_id, $cat)) {
            return $this->json([
                'status' => false,
                'message' => 'اجازه ویرایش این دسته را ندارید'
            ], 403);
        }

        $title = $this->input->post('title', true);

        if (!$title) {
            return $this->json([
                'status' => false,
                'message' => 'نام دسته الزامی است'
            ], 400);
        }

        if ($this->Category_model->update($id, $user_id, ['title' => $title])) {
            return $this->json([
                'status' => true,
                'message' => 'دسته‌بندی با موفقیت بروزرسانی شد'
            ]);
        }

        return $this->json([
            'status' => false,
            'message' => 'خطا در بروزرسانی دسته‌بندی'
        ], 500);
    }

    public function delete($id)
    {
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            return $this->json([
                'status' => false,
                'message' => 'ابتدا وارد حساب خود شوید'
            ], 401);
        }

        $cat = $this->db->get_where('categories', ['id' => $id])->row();

        if (!$this->category_policy->can_delete($user_id, $cat)) {
            return $this->json([
                'status' => false,
                'message' => 'اجازه حذف این دسته را ندارید'
            ], 403);
        }

        if ($this->Category_model->delete($id, $user_id)) {
            return $this->json([
                'status' => true,
                'message' => 'دسته حذف شد'
            ]);
        }

        return $this->json([
            'status' => false,
            'message' => 'خطا در حذف'
        ], 500);
    }

    public function restore($id)
    {
        $user_id = $this->session->userdata('user_id');

        if (!$user_id) {
            return $this->json([
                'status' => false,
                'message' => 'ابتدا وارد حساب خود شوید'
            ], 401);
        }

        if ($this->Category_model->restore($id, $user_id)) {
            return $this->json([
                'status' => true,
                'message' => 'دسته بازیابی شد'
            ]);
        }

        return $this->json([
            'status' => false,
            'message' => 'خطا در بازیابی دسته'
        ], 500);
    }
}
