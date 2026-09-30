<?php

class Transaction_service
{
    protected $model;
    protected $policy;

    public function __construct(
        Transaction_model $model,
        Transaction_policy $policy
    ) {
        $this->model = $model;
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
        $transaction = $this->model->get_by_id($id);

        if (!$transaction) {
            return [
                'status' => false,
                'code' => 404,
                'message' => 'تراکنش پیدا نشد'
            ];
        }

        if (!$this->policy->can_update($user_id, $transaction)) {
            return [
                'status' => false,
                'code' => 403,
                'message' => 'اجازه ویرایش این تراکنش را ندارید'
            ];
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
        $transaction = $this->model->get_by_id($id);

        if (!$transaction) {
            return [
                'status' => false,
                'code' => 404,
                'message' => 'تراکنش پیدا نشد'
            ];
        }

        if (!$this->policy->can_delete($user_id, $transaction)) {
            return [
                'status' => false,
                'code' => 403,
                'message' => 'اجازه حذف این تراکنش را ندارید'
            ];
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