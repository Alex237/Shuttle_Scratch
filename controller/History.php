<?php
require_once './controller/BaseController.php';

/**
 * The dasboard controller
 * 
 * @author Alex Maxime CADEVALL <a.cadevall@insta.fr>
 */
class History extends BaseController
{

    public function __construct() {
        parent::__construct();
        Session::run();
    }

    public function index() {
        /* $idUser = $this->session->userdata('idUser');
          $data = array(
          'ticketAssignedToMe' => $this->ticket->loadTicketsOpenBy($idUser),
          'ticketOpenByMe' => $this->ticket->loadTicketsAssignedTo($idUser)
          ); */
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
