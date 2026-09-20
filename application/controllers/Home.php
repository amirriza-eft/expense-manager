<?php

class Home extends CI_Controller {
    public function index() {
        $data['page_title'] = 'خانه';
        $this->load->view('components/header', $data);
        $this->load->view('home', $data);
        $this->load->view('components/footer', $data);
    }
}