<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Browsers extends CI_Controller
{

    /**
     * 
     */
    public function index() {
        $this->load->view('layout', array('content' => 'browsers'));
    }

}