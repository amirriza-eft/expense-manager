<?php

class Home extends CI_Controller {
    public function index() {
        $data['page_title'] = 'خانه';
        $this->load->view('home', $data);
    }
}