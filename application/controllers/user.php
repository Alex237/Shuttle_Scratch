<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User extends CI_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->library('twig');
        $this->twig->ci_function_init();
    }

    public function index() {

        $this->twig->display('users.html.twig', array(
            'users' => $this->user_model->loadAll()
        ));
    }

    public function create() {

        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class="input-error">', '</div>');

            $this->form_validation->set_rules('company', 'Société', 'trim|required|strtolower|max_length[255]');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|strtolower|valid_email|callback_uniqueEmail');
            $this->form_validation->set_rules('password', 'Mot de passe', 'trim|required');
            $this->form_validation->set_rules('confirmation', 'Confirmation', 'required|matches[password]');
            $this->form_validation->set_rules('firstname', 'Prenom', 'max_length[45]');
            $this->form_validation->set_rules('lastname', 'Nom', 'max_length[45]');

            if ($this->form_validation->run()) {

                $now = new \DateTime();
                $data = array(
                    'company' => $this->input->post('company'),
                    'email' => $this->input->post('email'),
                    'password' => md5($this->input->post('password')),
                    'registerDate' => $now->format('Y-m-d H:i:s'),
                    'firstname' => $this->input->post('firstname'),
                    'lastname' => $this->input->post('lastname')
                );

                if ($this->user_model->save($data)) {
                    echo 'enregistré';
                    return true;
                } else {
                    echo 'erreur à l\'enregistrement';
                    return false;
                }
            }
        }

        $this->twig->display('user/create.html.twig');
    }

    public function show($idUser) {

        $this->twig->display('user/create.html.twig', array(
            'user' => $this->user_model->loadById($idUser)
        ));
    }

    public function edit($idUser) {

        //
    }

    public function delete($idUser) {

        $this->user_model->delete($idUser);
    }

    public function login() {

        $this->checkUserAgent();

        $data = array();

        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class="input-error">', '</div>');

            $this->form_validation->set_rules('email', 'Email', 'trim|required|strtolower|valid_email');
            $this->form_validation->set_rules('password', 'Mot de passe', 'trim|required');

            if ($this->form_validation->run()) {

                $email = $this->input->post('email');
                $password = md5($this->input->post('password'));

                if ($this->user_model->authentification($email, $password)) {
                    redirect('dashboard');
                } else {
                    $data['error'] = 'Identifiants incorrects';
                }
            }
        }

        $this->twig->display('user/login.html.twig', $data);
    }

    public function logout() {

        $this->user_model->logout();
        redirect('login');
    }

    public function register() {

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

                if ($this->user_model->save($data)) {
                    echo 'enregistré';
                    return true;
                } else {
                    echo 'erreur à l\'enregistrement';
                    return false;
                }
            }
        }

        $this->twig->display('user/register.html.twig');
    }

    public function checkUserAgent() {

        $this->load->library('user_agent');
        if ($this->agent->is_browser('Internet Explorer') OR $this->agent->is_browser('MSIE')) {
            $this->load->helper('url');
            $this->twig->display('user/browsers.html.twig');
            die;
        }
    }

    public function uniqueEmail($email) {

        $this->form_validation->set_message('uniqueEmail', 'Un compte existe déjà pour cet Email');
        return $this->user_model->isUniqueEmail($email);
    }

}