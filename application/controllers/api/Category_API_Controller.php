<?php

defined('BASEPATH') OR exit('No direct script access allowed');


require_once(APPPATH.'controllers/api/Base_API_Controller.php');


class Category_API_Controller extends Base_API_Controller
{

    protected $category_service;


    public function __construct()
    {
        parent::__construct();

        $this->load->model('Category_model');

        require_once APPPATH.'services/Category_service.php';
        require_once APPPATH.'policies/Category_policy.php';

        $policy = new Category_policy();

        $this->category_service =
            new Category_service(
                $this->Category_model,
                $policy
            );
    }

    private function user()
    {
        return $this->session->userdata('user_id');
    }

    public function index()
    {
        $user_id = $this->auth_user();

        return $this->json(

            $this->category_service
                ->index($user_id)
        );
    }

    public function deleted()
    {
        $user_id = $this->auth_user();

        return $this->json(

            $this->category_service
                ->deleted($user_id)
        );
    }


    public function create()
    {
        $user_id=$this->user();

        $result =
            $this->category_service
                ->create(
                    $user_id,
                    $this->input->post('title',true)
                );

        return $this->json(
            $result,
            $result['code'] ?? 200
        );

    }

    public function update($id)
    {
        $result =
            $this->category_service
                ->update(
                    $this->user(),
                    $id,
                    $this->input->post('title',true)
                );

        return $this->json(
            $result,
            $result['code'] ?? 200
        );
    }

    public function delete($id)
    {
        $result =
            $this->category_service
                ->delete(
                    $this->user(),
                    $id
                );

        return $this->json(
            $result,
            $result['code'] ?? 200
        );

    }

    public function restore($id)
    {
        $result =
            $this->category_service
                ->restore(
                    $this->user(),
                    $id
                );

        return $this->json(
            $result,
            $result['code'] ?? 200
        );
    }
}