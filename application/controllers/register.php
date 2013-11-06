<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Register extends CI_Controller
{

    /**
     * 
     */
    public function index() {

        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class="input-error">', '</div>');

            $this->form_validation->set_rules('company', 'Société', 'trim|required|strtolower|max_length[255]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|strtolower|valid_email|callback_uniqueEmail');
            $this->form_validation->set_rules('password', 'Mot de passe', 'trim|required');
            $this->form_validation->set_rules('confirmation', 'Confirmation', 'required|matches[password]');

            if ($this->form_validation->run()) {

                $now = new \DateTime();
                $data = array(
                    'company' => $this->input->post('company'),
                    'email' => $this->input->post('email'),
                    'password' => md5($this->input->post('password')),
                    'registerDate' => $now->format('Y-m-d H:i:s'),
                );

                if ($this->user->save($data)) {
                    echo 'enregistré';
                    return true;
                } else {
                    echo 'erreur à l\'enregistrement';
                    return false;
                }
            }
        }

        $this->load->view('layout', array('content' => 'register'));
    }

    public function uniqueEmail($email) {
        $this->form_validation->set_message('uniqueEmail', 'Un compte existe déjà pour cet Email');
        return $this->user->isUniqueEmail($email);
    }

}