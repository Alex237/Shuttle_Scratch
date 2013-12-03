<?php

require_once './controller/BaseController.php';
require_once './core/validator.php';

/**
 * The project controller
 * 
 * @author Fabien MORCHOISNE <f.morchoisne@insta.fr>
 */
class Project extends BaseController
{

    /**
     * Constructor
     * 
     */
    public function __construct() {
        parent::__construct("ProjectModel");
        $this->restrict();
    }

    /**
     * Index
     * 
     */
    public function index() {
        $this->model->init();

        $this->twig->display('project/overview.html.twig', array(
            'projects' => $this->model->loadAll()
        ));

        $this->model->close();
    }

    /**
     * Add a new project
     * 
     */
    public function add() {

        if ($_SERVER['REQUEST_METHOD'] == 'POST' and !empty($_POST)) {
            $this->validator = new Validator();

            $this->validator->addRules('name', 'required')
                    ->addRules('deadline', 'validdate');

            if ($this->validator->run()) {

                $this->model->init();

                if ($this->model->projectExists($_POST['name'])) {

                    $this->validator->addCustomError('name', 'Un projet porte déjà ce nom');
                } else {

                    $now = new \DateTime();
                    $project = array(
                        'name' => $_POST['name'],
                        'createDate' => $now->format('Y-m-d H:i:s'),
                        'deadline' => implode('-', array_reverse(explode('/', $_POST['deadline'])))
                    );

                    if ($this->model->save($project)) {

                        $this->session->addFlash('Projet Crée', 'success');
                        $this->redirect('/project');
                    } else {

                        $this->alert('Impossible de créer le projet', 'danger');
                    }
                }

                $this->model->close();
            }
        }

        $this->twig->display('project/add.html.twig');
    }

    public function delete($id) {
        
    }

    public function edit($id) {
        
    }

    public function show($id) {
        
    }

}

