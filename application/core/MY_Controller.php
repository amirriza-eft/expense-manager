<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class MY_Controller extends CI_Controller
{

    protected function json($data, $status = 200)
    {
        $this->output
            ->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(
                json_encode(
                    $data,
                    JSON_UNESCAPED_UNICODE
                )
            );

        return;
    }

    protected function require_login()
    {
        if (!$this->session->userdata('user_id')) {

            $this->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);

            exit;
        }
    }
}