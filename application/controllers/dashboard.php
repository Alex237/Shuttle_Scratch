<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->library('twig');
        $this->twig->ci_function_init();
        
        $this->load->model('user_model', 'user');
    }

    public function overview() {
        var_dump($this->session->userdata('roles'));
        var_dump($this->user->isGranted('client'));
        var_dump($this->user->isGranted('admin'));
        
        $this->twig->display('dashboard/overview.html.twig');
    }

}