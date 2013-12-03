<?php

require_once './controller/BaseController.php';

/**
 * The dasboard controller
 * 
 * @author Alex Maxime CADEVALL <a.cadevall@insta.fr>
 */
class Tickets extends BaseController
{
    /*
     * Constructor
     */

    public function __construct() {
        parent::__construct("TicketsModel");
        $this->restrict();
    }

    /*
     * index view
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

    /*
     * add view
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
                    $msg = 'Le ticket ": ' . $_POST['title'] . '" a bien été valider';
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

    /*
     * show view
     */

    public function show($idTicket) {
        $this->model->init();
        $tickeData = $this->model->loadById($idTicket);

        $this->model->flush();
        $lastId = $this->model->getLastId();
        $previous = $next = $idTicket;
        do {
            $next = $next + 1;
            if ($next <= $lastId['idTicket']) {
                $this->model->flush();
                $nextTicket = $this->model->loadById($next);
            } else {
                $nextTicket = null;
                break;
            }
        } while (($nextTicket == FALSE) && $next <= $lastId['idTicket']);

        $this->model->flush();
        do {
            $previous = $previous - 1;
            if (($previous > 1) && ($previous <= $lastId['idTicket'])) {
                $this->model->flush();
                $previousTicket = $this->model->loadById($previous);
            } else {
                $previousTicket = null;
                break;
            }
        } while (($previousTicket == FALSE) && ($previous > 1) && ($previous <= $lastId['idTicket']));

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

    /*
     * delete view
     */

    public function delete($idTicket) {
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
    }

    /*
     * edit view
     */

    public function edit($idTicket) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' and !empty($_POST)) {
            $data = array(
                'closeDate' => date('Y-m-d')
            );
            $this->model->init();
            if ($this->model->closeTicket($idTicket)) {
                $msg = 'Le ticket numéros : ' . $idTicket . ' a bien été modifié';
                $data = array(
                    'action' => 'edit',
                    'view' => 'tickets',
                    'msg' => $msg
                );
                $this->model->close();
                $this->twig->display('info/msg.success.request.twig', $data);
            } else {
                $msg = "Le ticket numéros : " . $idTicket . " n'a pas pu être modifié";
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
                $msg = "Le status du ticket numéros : " . $idTicket . " n'a pas pu être modifié";
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
                $msg = "Le ticket numéros : " . $idTicket . " n'a pas pu être réouvert";
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
