<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH.'controllers/api/Base_API_Controller.php');

class Transaction_API_Controller extends Base_API_Controller
{
    protected $service;
    protected $validator;


    public function __construct()
    {
        parent::__construct();

        $this->load->library('session');

        require_once APPPATH.'services/Transaction_service.php';
        require_once APPPATH.'validators/Transaction_validator.php';

        $this->service = new Transaction_service();
        $this->validator = new Transaction_validator();
    }


    public function index()
    {
        $user_id = $this->session->userdata('user_id');

        if(!$user_id){

            return $this->json([
                'status'=>false,
                'message'=>'ابتدا وارد شوید'
            ],401);
        }


        return $this->json(
            $this->service->index(
                $user_id,
                $this->input->get()
            )
        );
    }



    public function create()
    {
        $user_id = $this->session->userdata('user_id');


        if(!$this->validator->validate_create()){

            return $this->json([
                'status'=>false,
                'message'=>$this->validator->errors()
            ],400);
        }


        $result = $this->service->create($user_id);


        return $this->json(
            $result,
            $result['code'] ?? 200
        );
    }



    public function update()
    {
        $user_id = $this->session->userdata('user_id');


        if(!$user_id){

            return $this->json([
                'status'=>false,
                'message'=>'ابتدا وارد شوید'
            ],401);
        }


        if(!$this->validator->validate_update()){

            return $this->json([
                'status'=>false,
                'message'=>$this->validator->errors()
            ],400);
        }


        $id = $this->input->post('id',true);


        $result = $this->service->update(
            $user_id,
            $id
        );


        return $this->json(
            $result,
            $result['code'] ?? 200
        );
    }



    public function delete()
    {
        $user_id = $this->session->userdata('user_id');


        if(!$user_id){

            return $this->json([
                'status'=>false,
                'message'=>'ابتدا وارد شوید'
            ],401);
        }


        $id = $this->input->post('id',true);


        $result = $this->service->delete(
            $user_id,
            $id
        );


        return $this->json(
            $result,
            $result['code'] ?? 200
        );
    }



    public function deleted()
    {
        $user_id = $this->session->userdata('user_id');


        if(!$user_id){

            return $this->json([
                'status'=>false,
                'message'=>'ابتدا وارد شوید'
            ],401);
        }


        return $this->json(
            $this->service->deleted($user_id)
        );
    }



    public function restore()
    {
        $user_id = $this->session->userdata('user_id');


        $id = $this->input->post('id',true);


        $result = $this->service->restore(
            $user_id,
            $id
        );


        return $this->json(
            $result,
            $result['code'] ?? 200
        );
    }
}