<?php

class Transaction_service
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->model('Transaction_model');

        require_once APPPATH.'policies/Transaction_policy.php';

        $this->policy = new Transaction_policy();
    }

    public function create($user_id)
    {
        if (!$this->policy->can_create($user_id)) {

            return [
                'status'=>false,
                'code'=>401,
                'message'=>'ابتدا وارد حساب شوید'
            ];
        }

        $data = [
            'user_id'=>$user_id,
            'category_id'=>$this->CI->input->post('category_id',true) ?: null,
            'title'=>$this->CI->input->post('title',true),
            'amount'=>$this->CI->input->post('amount',true),
            'type'=>$this->CI->input->post('type',true),
            'transaction_date'=>$this->CI->input->post('transaction_date',true),
            'description'=>$this->CI->input->post('description',true)
        ];

        if ($this->CI->Transaction_model->create($data)) {

            return [
                'status'=>true,
                'message'=>'تراکنش با موفقیت ثبت شد'
            ];
        }

        return [
            'status'=>false,
            'code'=>500,
            'message'=>'خطا در ثبت تراکنش'
        ];
    }


    public function update($user_id,$id)
    {
        $transaction = $this->CI->Transaction_model->find($id);

        if (!$this->policy->can_update($user_id,$transaction)) {

            return [
                'status'=>false,
                'code'=>403,
                'message'=>'اجازه ویرایش ندارید'
            ];
        }

        $data = [
            'category_id'=>$this->CI->input->post('category_id',true) ?: null,
            'title'=>$this->CI->input->post('title',true),
            'amount'=>$this->CI->input->post('amount',true),
            'type'=>$this->CI->input->post('type',true),
            'transaction_date'=>$this->CI->input->post('transaction_date',true),
            'description'=>$this->CI->input->post('description',true)
        ];

        if ($this->CI->Transaction_model->update($id,$user_id,$data)) {

            return [
                'status'=>true,
                'message'=>'تراکنش بروزرسانی شد'
            ];
        }

        return [
            'status'=>false,
            'code'=>500,
            'message'=>'خطا در بروزرسانی'
        ];
    }



    public function delete($user_id,$id)
    {
        $transaction = $this->CI->Transaction_model->find($id);

        if (!$this->policy->can_delete($user_id,$transaction)) {

            return [
                'status'=>false,
                'code'=>403,
                'message'=>'اجازه حذف ندارید'
            ];
        }

        if ($this->CI->Transaction_model->delete($id,$user_id)) {

            return [
                'status'=>true,
                'message'=>'تراکنش حذف شد'
            ];
        }

        return [
            'status'=>false,
            'code'=>500,
            'message'=>'خطا در حذف'
        ];
    }
}