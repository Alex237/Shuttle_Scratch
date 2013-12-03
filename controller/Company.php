<?php
require_once './controller/BaseController.php';

/**
 * The dasboard controller
 * 
 * @author Alex Maxime CADEVALL <a.cadevall@insta.fr>
 */
class Company extends BaseController
{

    /**
     * Constructor
     * 
     */
    public function __construct() {
        parent::__construct();
        Session::run();
    }

    public function index() {
        // $data = array(
        //	'companies' => $this->company_model->loadAll()
        //);
        $this->twig->display('company/overview.html.twig');
    }

    public function create() {
        //if ($this->input->server('REQUEST_METHOD') == 'POST') {
        /*
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
          } */
        $this->twig->display('ticket/create.html.twig');
    }

    public function add() {
        //to do
    }

    public function delete($id) {
        //to do
    }

    public function edit($id) {
        //to do
    }

    public function show($id) {
        $this->twig->display('ticket/show.html.twig');
    }

}
