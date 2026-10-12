<?php

defined('BASEPATH') or exit('No direct script access allowed');

require_once(APPPATH . 'controllers/api/Base_API_Controller.php');

class Transaction_API_Controller extends Base_API_Controller
{
    protected $transaction_service;
    protected $validator;

    public function __construct()
    {
        parent::__construct();

        $this->load->model('Transaction_model');
        $this->load->model('Category_model');

        require_once APPPATH . 'services/Transaction_service.php';
        require_once APPPATH . 'validators/Transaction_validator.php';
        require_once APPPATH . 'policies/Transaction_policy.php';

        $this->transaction_service = new Transaction_service(
            $this->Transaction_model,
            $this->Category_model,
            new Transaction_policy()
        );

        $this->validator =
            new Transaction_validator(
            $this->form_validation
        );
    }

    public function index()
    {
        $user_id = $this->auth_user();

        return $this->json(
            $this->transaction_service->index(
                $user_id,
                $this->input->get()
            )
        );
    }

    public function create()
    {
        $user_id = $this->auth_user();

        if (!$this->validator->create()) {
            return $this->validation_error();
        }

        $result = $this->transaction_service->create(
            $user_id,
            $this->transaction_input()
        );

        return $this->json(
            $result,
            $result['code'] ?? 200
        );
    }

    public function update()
    {
        $user_id = $this->auth_user();

        if (!$this->validator->update()) {
            return $this->validation_error();
        }

        $result = $this->transaction_service->update(
            $user_id,
            $this->input->post('id', true),
            $this->transaction_input()
        );

        return $this->json(
            $result,
            $result['code'] ?? 200
        );
    }

    public function delete()
    {
        $user_id = $this->auth_user();

        $result = $this->transaction_service->delete(
            $user_id,
            $this->input->post('id', true)
        );

        return $this->json(
            $result,
            $result['code'] ?? 200
        );
    }

    public function deleted()
    {
        $user_id = $this->auth_user();

        return $this->json(
            $this->transaction_service->deleted($user_id)
        );
    }

    public function restore()
    {
        $user_id = $this->auth_user();

        $result = $this->transaction_service->restore(
            $user_id,
            $this->input->post('id', true)
        );

        return $this->json(
            $result,
            $result['code'] ?? 200
        );
    }

    private function transaction_input()
    {
        return [
            'category_id' => $this->input->post('category_id', true),
            'title' => $this->input->post('title', true),
            'amount' => $this->input->post('amount', true),
            'type' => $this->input->post('type', true),
            'transaction_date' => $this->input->post('transaction_date', true),
            'description' => $this->input->post('description', true)
        ];
    }

    private function validation_error()
    {
        return $this->json([
            'status' => false,
            'message' => strip_tags($this->validator->errors())
        ], 400);
    }
}
