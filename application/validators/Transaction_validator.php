<?php

class Transaction_validator
{
    protected $CI;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->CI->load->library('form_validation');
    }


    public function validate_create()
    {
        $this->set_rules();

        return $this->CI->form_validation->run();
    }


    public function validate_update()
    {
        $this->set_rules();

        return $this->CI->form_validation->run();
    }


    public function errors()
    {
        return validation_errors();
    }


    private function set_rules()
    {
        $this->CI->form_validation->set_rules(
            'title',
            'عنوان',
            'required|min_length[2]|max_length[100]',
            [
                'required'=>'عنوان الزامی است',
                'min_length'=>'عنوان کوتاه است'
            ]
        );


        $this->CI->form_validation->set_rules(
            'amount',
            'مبلغ',
            'required|numeric',
            [
                'required'=>'مبلغ الزامی است',
                'numeric'=>'مبلغ باید عدد باشد'
            ]
        );


        $this->CI->form_validation->set_rules(
            'type',
            'نوع',
            'required|in_list[income,expense]',
            [
                'required'=>'نوع تراکنش الزامی است'
            ]
        );


        $this->CI->form_validation->set_rules(
            'transaction_date',
            'تاریخ',
            'required'
        );
    }
}