<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Login extends CI_Controller
{

    /**
     * 
     */
    public function index() {
        
        $this->load->library('user_agent');
        if ($this->agent->is_browser('Internet Explorer') OR $this->agent->is_browser('MSIE')) {
            $this->load->helper('url');
            return redirect('browsers');
        }
        
        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class="input-error">', '</div>');

            $this->form_validation->set_rules('email', 'Email', 'trim|required|strtolower|valid_email|callback_uniqueEmail');
            $this->form_validation->set_rules('password', 'Mot de passe', 'trim|required');

            if ($this->form_validation->run()) {

                if($this->user->authentification($this->input->post('email'), $this->input->post('password'))) {
                    var_dump('connecté');
                    var_dump($this->session->userdata());
                } else {
                    var_dump('erreur email/mot de passe');
                }
                die;
            }
        }


        $this->load->view('layout', array('content' => 'login'));
    }
   

}