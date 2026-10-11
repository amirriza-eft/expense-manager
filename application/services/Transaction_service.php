<?php

class Transaction_service
{
    protected $model;
    protected $category_model;
    protected $policy;

    public function __construct(
        Transaction_model $model,
        Category_model $category_model,
        Transaction_policy $policy
    ) {
        $this->model = $model;
        $this->category_model = $category_model;
        $this->policy = $policy;
    }

    public function index($user_id, $params = [])
    {
        $page = (int)($params['page'] ?? 1);

        $limit = 5;

        $offset = ($page - 1) * $limit;

        $transactions = $this->model->get_user_transactions(
            $user_id,
            $limit,
            $offset,
            $params['search'] ?? null,
            $params['type'] ?? null,
            $params['category_id'] ?? null,
            $params['from_date'] ?? null,
            $params['to_date'] ?? null,
            $params['sort'] ?? null
        );

        $total = $this->model->count_user_transactions(
            $user_id,
            $params['search'] ?? null,
            $params['type'] ?? null,
            $params['category_id'] ?? null,
            $params['from_date'] ?? null,
            $params['to_date'] ?? null
        );

        return [
            'status' => true,
            'transactions' => $transactions,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => ceil($total / $limit),
                'total' => $total
            ]
        ];
    }

    public function create($user_id, $data)
    {
        if (!$this->policy->can_create($user_id)) {
            return [
                'status' => false,
                'code' => 401,
                'message' => 'ابتدا وارد حساب شوید'
            ];
        }

        $category_error = $this->validate_category(
            $user_id,
            $data['category_id'] ?? null,
            $data['type'] ?? null
        );

        if ($category_error !== null) {
            return $category_error;
        }

        $data['user_id'] = $user_id;

        if ($this->model->create($data)) {
            return [
                'status' => true,
                'message' => 'تراکنش با موفقیت ثبت شد'
            ];
        }

        return [
            'status' => false,
            'code' => 500,
            'message' => 'خطا در ثبت تراکنش'
        ];
    }

    public function update($user_id, $id, $data)
    {
        $access_error = $this->authorize_transaction(
            $user_id,
            $id,
            'update'
        );

        if ($access_error !== null) {
            return $access_error;
        }

        $category_error = $this->validate_category(
            $user_id,
            $data['category_id'] ?? null,
            $data['type'] ?? null
        );

        if ($category_error !== null) {
            return $category_error;
        }

        if ($this->model->update($id, $user_id, $data)) {
            return [
                'status' => true,
                'message' => 'تراکنش بروزرسانی شد'
            ];
        }

        return [
            'status' => false,
            'code' => 500,
            'message' => 'خطا در بروزرسانی'
        ];
    }

    public function delete($user_id, $id)
    {
        $access_error = $this->authorize_transaction(
            $user_id,
            $id,
            'delete'
        );

        if ($access_error !== null) {
            return $access_error;
        }

        if ($this->model->delete($id, $user_id)) {
            return [
                'status' => true,
                'message' => 'تراکنش حذف شد'
            ];
        }

        return [
            'status' => false,
            'code' => 500,
            'message' => 'خطا در حذف'
        ];
    }

    private function authorize_transaction($user_id, $id, $action)
    {
        $transaction = $this->model->get_by_id($id);

        if (!$transaction) {
            return [
                'status' => false,
                'code' => 404,
                'message' => 'تراکنش پیدا نشد'
            ];
        }

        $allowed = $action === 'delete'
            ? $this->policy->can_delete($user_id, $transaction)
            : $this->policy->can_update($user_id, $transaction);

        if (!$allowed) {
            return [
                'status' => false,
                'code' => 403,
                'message' => $action === 'delete'
                    ? 'اجازه حذف این تراکنش را ندارید'
                    : 'اجازه ویرایش این تراکنش را ندارید'
            ];
        }

        return null;
    }

    private function validate_category($user_id, $category_id, $type)
    {
        if ($category_id === null || $category_id === '') {
            return [
                'status' => false,
                'code' => 400,
                'message' => 'انتخاب دسته‌بندی الزامی است'
            ];
        }

        $category = $this->category_model->get_active_by_id(
            $category_id,
            $user_id
        );

        if (!$category) {
            return [
                'status' => false,
                'code' => 400,
                'message' => 'دسته‌بندی معتبر نیست'
            ];
        }

        if ($category->type !== $type) {
            return [
                'status' => false,
                'code' => 400,
                'message' => 'نوع دسته‌بندی با نوع تراکنش هم‌خوانی ندارد'
            ];
        }

        return null;
    }

    public function deleted($user_id)
    {
        return [
            'status' => true,
            'transactions' => $this->model->get_user_deleted_transactions($user_id)
        ];
    }

    public function restore($user_id, $id)
    {
        if ($this->model->restore($id, $user_id)) {
            return [
                'status' => true,
                'message' => 'تراکنش بازیابی شد'
            ];
        }

        return [
            'status' => false,
            'code' => 500,
            'message' => 'خطا در بازیابی تراکنش'
        ];
    }
}