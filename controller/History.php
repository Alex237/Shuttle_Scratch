<?php
/**
 * The dasboard controller
 * 
 * @author Alex Maxime CADEVALL <a.cadevall@insta.fr>
 */

require_once './Controller/BaseController.php';
Session::run();

class History extends BaseController{

	public function __construct() {
		parent::__construct();
		/*$this->load->library('twig');
		 $this->twig->ci_function_init();*/
		//$this->load->model('ticket_model', 'ticket');
	}

	 public function index() {
		 /*$idUser = $this->session->userdata('idUser');
		 $data = array(
			 'ticketAssignedToMe' => $this->ticket->loadTicketsOpenBy($idUser),
			 'ticketOpenByMe' => $this->ticket->loadTicketsAssignedTo($idUser)
			);*/
		 $this->twig->display('history/overview.html.twig');
	 }

    public function show($id) {

        $data = array(
            'ticket' => $this->ticket->loadById($id)
        );

        $this->twig->display('history/show.html.twig', $data);
    }

	public function add() {
		
	}

	public function delete($id) {
		
	}

	public function edit($id) {
		
	}

}