<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Project extends CI_Controller
{

    public function __construct() {
        parent::__construct();
        $this->load->library('twig');
        $this->twig->ci_function_init();

        $this->load->model('project_model', 'project');
    }

    public function index() {
        $this->twig->display('project/overview.html.twig');
    }

    public function create() {

        $data = array();

        if ($this->input->server('REQUEST_METHOD') == 'POST') {

            $this->load->library('form_validation');
            $this->form_validation->set_error_delimiters('<div class="input-error">', '</div>');

            $this->form_validation->set_rules('name', 'Project', 'required|max_length[255]');
            $this->form_validation->set_rules('company', 'Société', '');
            $this->form_validation->set_rules('deadline', 'Deadline', '');

            if ($this->form_validation->run()) {

                $project = array(
                    'name' => $this->input->post('name'),
                    'company' => $this->input->post('company'),
                    'deadline' => $this->input->post('deadline')
                );

                if ($this->project->save($project)) {
                    redirect('dashboard');
                } else {
                    $data['error'] = 'Une erreur est survenue lors de la création du projet';
                }
            }
        }

        $this->twig->display('project/create.html.twig', $data);
    }

}