<?php
require_once './controller/BaseController.php';

/**
 * The dasboard controller
 * 
 * @author Alex Maxime CADEVALL <a.cadevall@insta.fr>
 */
class Projects extends BaseController{

	public function __construct() {
		parent::__construct();
		$this->restrict('team');
	}

	public function index() {
		$this->twig->display('project/overview.html.twig');
	}

	public function create() {
		//$data = array();
		/*if ($this->input->server('REQUEST_METHOD') == 'POST') {

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
        }*/
		$this->twig->display('project/create.html.twig');
	}

	public function add() {
		
	}

	public function delete($id) {
		
	}

	public function edit($id) {
		
	}

	public function show($id) {
		
	}
	
	

}
