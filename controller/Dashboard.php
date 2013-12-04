<?php

require_once './controller/BaseController.php';

class Dashboard extends BaseController
{

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct('UserModel');
        $this->restrict();
    }

    /**
     * The controller index
     */
    public function index() {
        $this->model->init();
        $idData = $this->session->getUserData();
        require_once './model/TicketsModel.php';
        $ticketModel =  new TicketsModel();
        $ticketModel->init();
        $ticketList = $ticketModel->loadAllTickets(0,3);
        $ticketModel->flush();
        $ticketOpened = $ticketModel->countTicketsOpenBy($idData['idUser']);
        $ticketModel->close();
        $user = $this->model->loadById($idData['idUser']);
        $user['roles'] = json_decode($user['roles']);
        $this->twig->display('dashboard/overview.html.twig', array(
            'userData' => $user,
            'ticketList' => $ticketList,
            'ticketOpened'=> $ticketOpened
        ));
        $this->model->close();
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
        //to do
    }

}
