<?php

require_once './controller/BaseController.php';

/**
 * The Tickets controller
 * 
 * @author Alex Maxime CADEVALL <a.cadevall@insta.fr>
 */
class Tickets extends BaseController {

    /**
     * Constructor
     * 
     * Load model and allox acces for logged person
     */
    public function __construct() {
        parent::__construct("TicketsModel");
        $this->restrict();
    }

    /**
     * index view
     * This will Load main view and show all ticket
     */
    public function index() {
        $this->model->init();
        $ticketList = $this->model->loadAllTickets();
        $this->model->flush();
        $totalTicket = $this->model->countAllTickets();
        $data = array(
            'ticketList' => $ticketList,
            'totalTickete' => $totalTicket
        );
        $this->model->close();
        $this->twig->display('tickets/overview.html.twig', $data);
    }

    /**
     * add view
     * This will Load view to create a new ticket
     */
    public function add() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' and !empty($_POST)) {
            require_once './core/validator.php';
            $validator = Validator::getInstance();

            $validator->addRules('title', 'required')
                    ->addRules('type', 'required')
                    ->addRules('detail', 'required')
                    ->addRules('startDate', 'required|validdate')
                    ->addRules('deadline', 'required|validdate');

            if ($validator->run()) {
                $this->model->init();
                $userdata = $this->session->getUserData();
                $ticket = array(
                    'idTicket' => '',
                    'type' => $_POST['type'],
                    'level' => $_POST['level'],
                    'openDate' => $_POST['startDate'],
                    'updateDate' => $_POST['startDate'],
                    'closeDate' => null,
                    'percent' => 0,
                    'openBy' => $userdata['idUser'],
                    'assignedTo' => $_POST['assignedTo'],
                    'deadline' => $_POST['deadline'],
                    'estimatedTime' => $_POST['estimatedTime'],
                    'title' => $_POST['title'],
                    'content' => $_POST['detail'],
                    'project' => $_POST['idProjet'],
                    'status' => $_POST['statusCange']
                );
                if ($this->model->createTicket($ticket)) {
                    $msg = 'Le ticket ": ' . $_POST['title'] . '" a bien été validé';
                    $data = array(
                        'action' => 'création',
                        'view' => 'tickets',
                        'msg' => $msg
                    );
                    $this->model->close();
                    $this->twig->display('info/msg.success.request.twig', $data);
                }
                exit;
            }
        }
        $this->model->init();
        $types = $this->model->loadTicketTypes();
        $this->model->flush();
        $projets = $this->model->loadProjectList();
        $this->model->flush();
        $teams = $this->model->loadMemberList();
        $team = array();
        for ($index = 0; $index < count($teams); $index++) {
            $roles = json_decode($teams[$index]['roles']);
            if (in_array('team', $roles) || in_array('admin', $roles)) {
                array_push($team, $teams[$index]);
            }
        }
        $data = array(
            'types' => $types,
            'projets' => $projets,
            'teams' => $team
        );
        $this->model->close();
        $this->twig->display('tickets/create.html.twig', $data);
    }

    /**
     * Show  view
     * This will show ticket
     * @param int $idTicket. The ticket id in database
     */
    public function show($idTicket) {
        $this->model->init();
        $tickeData = $this->model->loadById($idTicket);

        $this->model->flush();
        $nextTicket = $this->model->getNextId($idTicket);
        $this->model->flush();
        $previousTicket = $this->model->getPreviousId($idTicket);
        if ($tickeData) {
            $data = array(
                'action' => 'close',
                'actionMsg' => 'fermer',
                'view' => 'tickets',
                'id' => $idTicket,
                'ticket' => $tickeData,
                'nextTicket' => $nextTicket,
                'previousTicket' => $previousTicket
            );
            $this->model->close();
            $this->twig->display('tickets/show.html.twig', $data);
        } else {
            $msg = "Le du ticket numéros : " . $idTicket . " n'existe pas";
            $data = array(
                'view' => 'tickets',
                'msg' => $msg
            );
            $this->model->close();
            $this->twig->display('info/msg.failure.request.twig', $data);
        }
    }

    /**
     *  delete ticket in database.
     *  Only a admin or a team member can acces
     * @param int $idTicket. The ticket id in database
     */
    public function delete($idTicket) {
        if ($this->session->isGranted("admin") || $this->session->isGranted("team")) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST' and !empty($_POST)) {
                $this->model->init();
                if ($this->model->deleteById($idTicket)) {
                    $msg = 'Le ticket numéros : ' . $idTicket . ' a bien été effacé';
                    $data = array(
                        'action' => 'suppression',
                        'view' => 'tickets',
                        'msg' => $msg
                    );
                    $this->model->close();
                    $this->twig->display('info/msg.success.request.twig', $data);
                } else {
                    $msg = "Le ticket numéros : " . $idTicket . " n'a pas pu être supprimé";
                    $data = array(
                        'action' => 'suppression',
                        'view' => 'tickets',
                        'msg' => $msg
                    );
                    $this->model->close();
                    $this->twig->display('info/msg.failure.request.twig', $data);
                }
                exit;
            }
            $this->model->init();
            if ($this->model->loadById($idTicket)) {
                $data = array(
                    'action' => 'supprimer',
                    'view' => 'tickets',
                    'id' => $idTicket
                );
                $this->model->close();
                $this->twig->display('info/msg.confirmation.request.twig', $data);
            } else {
                $msg = "Le du ticket numéros : " . $idTicket . " n'existe pas";
                $data = array(
                    'view' => 'tickets',
                    'msg' => $msg
                );
                $this->model->close();
                $this->twig->display('info/msg.failure.request.twig', $data);
            }
        } else {
            $msg = "Vous n'êtes pas autoriser à faire cette action !";
            $data = array(
                'view' => 'tickets',
                'msg' => $msg
            );
            $this->twig->display('info/msg.failure.request.twig', $data);
            exit;
        }
    }

    /**
     * 
     * @param type $idTicket
     */
    public function edit($idTicket) {
        if ($this->session->isGranted("admin") || $this->session->isGranted("team")) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST' and !empty($_POST)) {
                $now = new \DateTime();
                $ticket = array(
                    'idTicket' => $idTicket,
                    'type' => $_POST['type'],
                    'level' => $_POST['level'],
                    'openDate' => $_POST['startDate'],
                    'updateDate' => $now->format('Y-m-d H:i:s'),
                    'assignedTo' => $_POST['assignedTo'],
                    'deadline' => $_POST['deadline'],
                    'estimatedTime' => $_POST['estimatedTime'],
                    'title' => $_POST['title'],
                    'content' => $_POST['detail'],
                    'project' => $_POST['idProjet'],
                    'status' => $_POST['statusCange']
                );
                var_dump($_POST);
                $data = array(
                    'action' => 'edit',
                    'view' => 'tickets',
                    'showdebug' => true,
                    'dumps' => $ticket
                ); 
                if ($this->model->saveTicket($ticket)) {
                    $msg = 'Le ticket : <b>"' . $_POST['title'] . '"</b> numéro : <b>' . $idTicket . '</b>" a bien été mis à jours';
                    $data = array(
                        'action' => 'création',
                        'view' => 'tickets',
                        'msg' => $msg
                    );
                    $this->model->close();
                    $this->twig->display('info/msg.success.request.twig', $data);
                    exit;
                }
                exit;
                $this->twig->display('info/msg.success.request.twig', $data);
                exit;
            }
            $this->model->init();
            $types = $this->model->loadTicketTypes();
            $this->model->flush();
            $tickeData = $this->model->loadById($idTicket);
            $this->model->flush();
            $projets = $this->model->loadProjectList();
            $this->model->flush();
            $teams = $this->model->loadMemberList();
            $team = array();
            for ($index = 0; $index < count($teams); $index++) {
                $roles = json_decode($teams[$index]['roles']);
                if (in_array('team', $roles) || in_array('admin', $roles)) {
                    array_push($team, $teams[$index]);
                }
            }
            if ($tickeData) {
                $data = array(
                    'action' => 'close',
                    'actionMsg' => 'fermer',
                    'view' => 'tickets',
                    'id' => $idTicket,
                    'ticket' => $tickeData,
                    'types' => $types,
                    'projets' => $projets,
                    'teams' => $team,
                    'showdebug' => true,
                    'dumps' => $tickeData
                );
                $this->model->close();
                $this->twig->display('tickets/edit.html.twig', $data);
            } else {
                $msg = "Le du ticket numéros : " . $idTicket . " n'existe pas";
                $data = array(
                    'view' => 'tickets',
                    'msg' => $msg
                );
                $this->model->close();
                $this->twig->display('info/msg.failure.request.twig', $data);
            }
        } else {
            $msg = "Vous n'êtes pas autoriser à faire cette action !";
            $data = array(
                'view' => 'tickets',
                'msg' => $msg
            );
            $this->twig->display('info/msg.failure.request.twig', $data);
            exit;
        }
    }

    public function close($idTicket) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' and !empty($_POST)) {
            $data = array(
                'closeDate' => date('Y-m-d')
            );
            $this->model->init();
            if ($this->model->closeTicket($idTicket)) {
                $msg = 'Le status du ticket numéros : ' . $idTicket . ' a bien été modifié';
                $data = array(
                    'action' => 'close',
                    'view' => 'tickets',
                    'msg' => $msg
                );
                $this->model->close();
                $this->twig->display('info/msg.success.request.twig', $data);
            } else {
                $msg = "Le status du ticket numéros : " . $idTicket . " n'a pas pu Ãªtre modifié";
                $data = array(
                    'action' => 'Changement de status',
                    'view' => 'tickets',
                    'msg' => $msg
                );
                $this->twig->display('info/msg.failure.request.twig', $data);
            }
            exit;
        }
        $this->model->init();
        if ($this->model->loadById($idTicket)) {
            $data = array(
                'action' => 'close',
                'actionMsg' => 'fermer',
                'view' => 'tickets',
                'id' => $idTicket
            );
            $this->model->close();
            $this->twig->display('info/msg.confirmation.request.twig', $data);
        } else {
            $msg = "Le ticket numéros : " . $idTicket . " n'existe pas";
            $data = array(
                'view' => 'tickets',
                'msg' => $msg
            );
            $this->model->close();
            $this->twig->display('info/msg.failure.request.twig', $data);
        }
    }

    public function reopen($idTicket) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' and !empty($_POST)) {
            $data = array(
                'closeDate' => null
            );
            $this->model->init();
            if ($this->model->reopenTicket($idTicket)) {
                $msg = 'Le ticket numéros : ' . $idTicket . ' a bien été réouvert';
                $data = array(
                    'action' => 'reopen',
                    'view' => 'tickets',
                    'id' => $idTicket,
                    'msg' => $msg
                );
                $this->model->close();
                $this->twig->display('info/msg.success.request.twig', $data);
            } else {
                $msg = "Le ticket numéros : " . $idTicket . " n'a pas pu Ãªtre réouvert";
                $data = array(
                    'action' => 'Changement de status',
                    'view' => 'tickets',
                    'msg' => $msg,
                    'id' => $idTicket
                );
                $this->twig->display('info/msg.failure.request.twig', $data);
            }
            exit;
        }
        $this->model->init();
        if ($this->model->loadById($idTicket)) {
            $data = array(
                'action' => 'reopen',
                'actionMsg' => 'réouvrir',
                'view' => 'tickets',
                'id' => $idTicket
            );
            $this->model->close();
            $this->twig->display('info/msg.confirmation.request.twig', $data);
        } else {
            $msg = "Le ticket numéros : " . $idTicket . " n'existe pas";
            $data = array(
                'view' => 'tickets',
                'msg' => $msg
            );
            $this->model->close();
            $this->twig->display('info/msg.failure.request.twig', $data);
        }
    }

}

