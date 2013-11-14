<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Company extends CI_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->library('twig');
        $this->twig->ci_function_init();

        $this->load->model('company_model', 'company');
    }

    public function index() {
        
        $data = array(
            'companies' => $this->company_model->loadAll()
        );
        
        $this->twig->display('company/overview.html.twig', $data);
    }

    public function create() {

        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class="input-error">', '</div>');

            $this->form_validation->set_rules('title', 'Titre', 'trim|required|max_length[255]');
            $this->form_validation->set_rules('type', 'Type', 'required');
            $this->form_validation->set_rules('content', 'Détail', '');
            $this->form_validation->set_rules('deadline', 'Date de fin', '');

            if ($this->form_validation->run()) {

                $ticket = array(
                    'title' => $this->input->post('title'),
                    'type' => $this->input->post('type'),
                    'content' => $this->input->post('content'),
                    'deadline' => $this->input->post('deadline'),
                    'openBy' => $this->session->userdata('idUser')
                );

                if ($this->ticket_model->save($ticket)) {
                    redirect('dashboard');
                } else {
                    $data['error'] = 'Identifiants incorrects';
                }
            }
        }

        $this->twig->display('ticket/create.html.twig', $data);
    }

    public function show($idTicket) {

        $data = array(
            'ticket' => $this->ticket_model->loadById($idTicket)
        );

        $this->twig->display('ticket/show.html.twig', $data);
    }

}