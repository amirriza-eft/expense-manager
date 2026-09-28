<?php

defined('BASEPATH') OR exit('No direct script access allowed');


class Category_service
{
    protected $category_model;
    protected $category_policy;


    public function __construct(
        $category_model,
        $category_policy
    )
    {
        $this->category_model = $category_model;
        $this->category_policy = $category_policy;
    }


    public function index($user_id)
    {
        return [
            'status'=>true,
            'categories'=>
                $this->category_model
                    ->get_user_categories($user_id)
        ];
    }


    public function deleted($user_id)
    {
        return [
            'status'=>true,
            'categories'=>
                $this->category_model
                    ->get_user_deleted_categories($user_id)
        ];
    }


    public function create($user_id,$title)
    {

        if( !$this->category_policy->can_create($user_id) )
        {
            return [
                'status'=>false,
                'message'=>'ابتدا وارد حساب خود شوید',
                'code'=>401
            ];
        }

        $data=[
            'user_id'=>$user_id,
            'title'=>$title
        ];

        if( $this->category_model->create($data) )
        {
            return [
                'status'=>true,
                'message'=>'دسته‌بندی با موفقیت ایجاد شد'
            ];
        }

        return [
            'status'=>false,
            'message'=>'خطا در ایجاد دسته‌بندی',
            'code'=>500
        ];
    }

    public function update($user_id,$id,$title)
    {
        $cat = $this->category_model->get_by_id($id,$user_id);

        if( !$this->category_policy->can_update($user_id,$cat) )
        {
            return [
                'status'=>false,
                'message'=>'اجازه ویرایش این دسته را ندارید',
                'code'=>403
            ];
        }

        if(!$title){
            return [
                'status'=>false,
                'message'=>'نام دسته الزامی است',
                'code'=>400
            ];
        }

        if(
            $this->category_model
                ->update(
                    $id,
                    $user_id,
                    [
                        'title'=>$title
                    ]
                )
        ){

            return [
                'status'=>true,
                'message'=>'دسته‌بندی با موفقیت بروزرسانی شد'
            ];

        }

        return [
            'status'=>false,
            'message'=>'خطا در بروزرسانی دسته‌بندی',
            'code'=>500
        ];
    }

    public function delete($user_id,$id)
    {
        $cat = $this->category_model->get_by_id($id,$user_id);

        if( !$this->category_policy->can_delete($user_id,$cat) )
        {
            return [
                'status'=>false,
                'message'=>'اجازه حذف این دسته را ندارید',
                'code'=>403
            ];
        }

        if( $this->category_model->delete($id,$user_id) )
        {
            return [
                'status'=>true,
                'message'=>'دسته حذف شد'
            ];
        }

        return [
            'status'=>false,
            'message'=>'خطا در حذف',
            'code'=>500
        ];
    }

    public function restore($user_id,$id)
    {

        if( $this->category_model->restore($id,$user_id) )
        {
            return [
                'status'=>true,
                'message'=>'دسته بازیابی شد'
            ];
        }

        return [
            'status'=>false,
            'message'=>'خطا در بازیابی دسته',
            'code'=>500
        ];
    }
}