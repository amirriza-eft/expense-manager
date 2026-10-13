<?php

defined('BASEPATH') OR exit('No direct script access allowed');


require_once(APPPATH.'controllers/api/Base_API_Controller.php');


class Auth_API_Controller extends Base_API_Controller
{
    protected $auth_service;
    protected $validator;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('User_model');
        $this->load->model('Remember_token_model');

        require_once APPPATH.'services/Auth_service.php';
        require_once APPPATH.'validators/Auth_validator.php';

        $this->auth_service =
            new Auth_service(
                $this->User_model,
                $this->session,
                $this->Remember_token_model
            );

        $this->validator =
            new Auth_validator(
                $this->form_validation
            );
    }

    public function register()
    {
        if(!$this->validator->register()){

            return $this->json([
                'status'=>false,
                'message'=>strip_tags(validation_errors())
            ],400);
        }

        return $this->json(

            $this->auth_service->register([
                'full_name'=> $this->input->post('full_name',true),
                'email'=> $this->input->post('email',true),
                'password'=> $this->input->post('password'),
                'security_question_id'=> $this->input->post('security_question_id', true),
                'security_answer'=> $this->input->post('security_answer', true)
            ])
        );
    }

    public function login()
    {
        $remember_me = $this->input->post('remember_me', true) === '1';

        return $this->json(

            $this->auth_service->login(
                $this->input->post('email', true),
                $this->input->post('password'),
                $remember_me
            )
        );
    }

    public function restore()
    {
        return $this->json(

            $this->auth_service->restore(
                $this->input->post('email',true),
                $this->input->post('password')
            )
        );
    }

    public function logout()
    {
        $this->auth_service->logout();

        return $this->json([
            'status' => true,
            'message' => 'خروج موفق'
        ]);
    }

    public function delete_account()
    {
        $user_id = $this->auth_user();

        return $this->json(

            $this->auth_service->delete(
                $user_id,
                $this->input->post('password')
            )
        );
    }

    public function recovery_question()
    {
        return $this->json(
            $this->auth_service->recovery_get_question(
                $this->input->post('email', true)
            )
        );
    }

    public function recovery_verify()
    {
        return $this->json(
            $this->auth_service->recovery_verify_answer(
                $this->input->post('email', true),
                $this->input->post('security_answer', true)
            )
        );
    }

    public function recovery_reset()
    {
        if (!$this->validator->recovery_reset()) {
            return $this->json([
                'status' => false,
                'message' => strip_tags(validation_errors())
            ], 400);
        }

        return $this->json(
            $this->auth_service->recovery_reset_password(
                $this->input->post('new_password'),
                $this->input->post('new_password_confirm')
            )
        );
    }
}
