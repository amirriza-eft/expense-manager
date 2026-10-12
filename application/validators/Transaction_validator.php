<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Transaction_validator
{
    protected $form_validation;

    public function __construct($form_validation)
    {
        $this->form_validation = $form_validation;
    }

    public function create()
    {
        $this->form_validation->set_rules(
            'title',
            'عنوان',
            'required|min_length[2]|max_length[100]',
            [
                'required'   => 'عنوان الزامی است',
                'min_length' => 'عنوان کوتاه است',
                'max_length' => 'عنوان نباید بیشتر از ۱۰۰ کاراکتر باشد'
            ]
        );

        $this->form_validation->set_rules(
            'amount',
            'مبلغ',
            'required|regex_match[/^\d+(\.\d{1,2})?$/]|greater_than[0]',
            [
                'required'     => 'مبلغ الزامی است',
                'regex_match'  => 'مبلغ باید عدد مثبت معتبر باشد',
                'greater_than' => 'مبلغ باید بزرگ‌تر از صفر باشد'
            ]
        );

        $this->form_validation->set_rules(
            'type',
            'نوع',
            'required|in_list[income,expense]',
            [
                'required' => 'نوع تراکنش الزامی است',
                'in_list'  => 'نوع تراکنش نامعتبر است'
            ]
        );

        $this->form_validation->set_rules(
            'category_id',
            'دسته‌بندی',
            'required|integer|greater_than[0]',
            [
                'required'     => 'انتخاب دسته‌بندی الزامی است',
                'integer'      => 'دسته‌بندی نامعتبر است',
                'greater_than' => 'دسته‌بندی نامعتبر است'
            ]
        );

        $this->form_validation->set_rules(
            'transaction_date',
            'تاریخ',
            'required',
            [
                'required' => 'تاریخ تراکنش الزامی است'
            ]
        );

        return $this->form_validation->run();
    }

    public function update()
    {
        return $this->create();
    }
}