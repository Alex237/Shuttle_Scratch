<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ticket extends CI_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->library('twig');
        $this->twig->ci_function_init();

        $this->load->model('ticket_model', 'ticket');
    }

    public function index() {
        
        $idUser = $this->session->userdata('idUser');
        
        $data = array(
            'ticketAssignedToMe' => $this->ticket->loadTicketsOpenBy($idUser),
            'ticketOpenByMe' => $this->ticket->loadTicketsAssignedTo($idUser)
        );

        $this->twig->display('ticket/overview.html.twig', $data);
    }

    public function create() {

        $data = array(
            'types' => $this->ticket->loadTicketTypes()
        );


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

                if ($this->ticket->save($ticket)) {
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
            'ticket' => $this->ticket->loadById($idTicket)
        );

        $this->twig->display('ticket/show.html.twig', $data);
    }

}