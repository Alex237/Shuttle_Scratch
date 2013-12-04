<?php

/**
 * The user model
 * 
 * @author Alex Maxime CADEVALL <a.cadevall@insta.fr>
 */
require_once './model/BaseModel.php';

class TicketsModel extends BaseModel
{

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct('ticket', 'idTicket');
    }

    /**
     * Load all tickets in database
     * @param int $offet The query offset
     * @param int $limit The query limit
     */
    public function loadAllTickets($offset = 0, $limit = 0) {
        $query = $this->select(array(
                            $this->table . '.*',
                            'u.*',
                            'ts.*',
                            'u2.firstname as assignedToFirstname',
                            'u2.lastname as assignedToLastname'
                        ))
                        ->from(array($this->table))
                        ->join('user u', 'u.idUser = ' . $this->table . '.openBy')
                        ->join('ticketstatus ts', 'ts.idStatus = ' . $this->table . '.status')
                        ->join('user u2', 'u2.idUser = ' . $this->table . '.assignedTo')
                        ->orderBy(array('idTicket'), 'DESC')
                        ->limit($offset, $limit)->buildQuery();

        $result = $this->db->prepare($query);
        $result->execute();
        return $result->fetchAll();
    }

    /**
     * Load ticket with ID as parameter
     * @param int $id the ticket ID 
     * @param string $columns not use in This Model
     * @return array Associative array of databes value
     */
    public function loadByIdJoined($idTicket) {
        $query = $this->select(array(
                    $this->table . '.*',
                    'u.*',
                    'tt.*',
                    'ts.*',
                    'u2.firstname as assignedToFirstname',
                    'u2.lastname as assignedToLastname',
                    'u2.email as assignedToEmail'
                ))
                ->from(array($this->table))
                ->join('user u', 'u.idUser = ' . $this->table . '.openBy')
                ->join('tickettype tt', 'tt.idTicketType = ' . $this->table . '.type')
                ->join('ticketstatus ts', 'ts.idStatus = ' . $this->table . '.status')
                ->join('user u2', 'u2.idUser = ' . $this->table . '.assignedTo')
                ->where(array($this->primaryKey . ' = :idTicket'))
                ->buildQuery();
        $result = $this->db->prepare($query);
        $result->execute(array(':idTicket' => $id));
        return $result->fetch();
    }

    /**
     * Load tickets open by an user
     * @param int $idUser the id User
     * @return array Associative array of databes value
     */
    public function loadTicketsOpenBy($idUser) {
        $query = $this->select()
                ->from(array($this->table))
                ->where(array('openBy = :openBy'))
                ->buildQuery();
        $result = $this->db->prepare($query);
        $result->execute(array(':openBy' => $idUser));
        return $result->fetch();
    }

    /**
     * Load ticket open by an user
     * @param int $idUser the id User
     * @return array Associative array of databes value
     */
    public function countTicketsOpenBy($idUser) {
        $query = $this->select(array('COUNT(*)'))
                ->from(array($this->table))
                ->where(array('openBy = :openBy'))
                ->buildQuery();
        $result = $this->db->prepare($query);
        $result->execute(array(':openBy' => $idUser));
        return $result->fetch();
    }

    /**
     * 
     * @param int $idUser
     * @return array Associative array of databes value
     */
    public function loadTicketsAssignedTo($idUser) {
        $query = $this->select()
                ->from(array($this->table))
                ->where(array('assignedTo = :assignedTo'))
                ->buildQuery();
        $result = $this->db->prepare($query);
        $result->execute(array(':assignedTo' => $idUser));
        return $result->fetch();
        /* $query = $this->db->select()
          ->from($this->table_name)
          ->where('assignedTo', $idUser);

          return $query->get()->result(); */
    }

    /**
     * Load list of ticket
     * @return array Associative array of databes value
     */
    public function loadTicketTypes() {
        $query = $this->select()
                ->from(array('tickettype'))
                ->buildQuery();
        $result = $this->db->prepare($query);
        $result->execute();
        return $result->fetchAll();
    }

    /**
     * Load list of status
     * @return array Associative array of databes value
     */
    public function loadStatusList() {
        $query = $this->select()
                ->from(array('ticketstatus'))
                ->buildQuery();
        $result = $this->db->query($query);
        return $result->fetchAll();
    }

    /**
     * Load list of project type list
     * @return array Associative array of databes value
     */
    public function loadProjectList() {
        $query = $this->select()
                ->from(array('project'))
                ->buildQuery();
        $result = $this->db->prepare($query);
        $result->execute();
        return $result->fetchAll();
    }

    /**
     * * Load list of member
     * @return array Associative array of databes value
     */
    public function loadMemberList() {
        $query = $this->select(array('idUser', 'firstname', 'lastname', 'roles'))
                ->from(array('user'))
                ->buildQuery();
        $result = $this->db->prepare($query);
        $result->execute();
        return $result->fetchAll();
    }

    /**
     * Change the status of a ticket to close
     * @param int $idTicket The id of ticket in database
     * @return boolean TRUE on succes, else FALSE
     */
    public function closeTicket($idTicket) {
        if ($idTicket != null && is_numeric($idTicket)) {
            $now = new \DateTime();
            $sql = 'UPDATE ';
            $sql .= $this->table;
            $sql .= ' SET closeDate = :closeDate, status = :status WHERE idTicket = :idTicket';
            $data = array(
                'closeDate' => $now->format('Y-m-d H:i:s'),
                'idTicket' => $idTicket,
                'status' => 8
            );
            $this->db->exec('SET foreign_key_checks = 0');
            $insert = $this->db->prepare($sql);
            $result = $insert->execute($data);
            $this->db->exec('SET foreign_key_checks = 1');
            return $result;
        }
    }

    /**
     * save ticket after edition
     * @param array $data associative array of data
     * @param type $idTicket the id of ticket where to save
     * @return boolean TRUE on succes, else FALSE
     */
    public function saveTicket($data, $idTicket) {
        $sql = 'UPDATE ';
        $sql .= $this->table;
        $sql .= ' SET ';
        foreach ($data as $key => $value) {
            $sql .= $key . ' = :' . $key . ', ';
        }
        $columns = array_keys($data);
        foreach ($columns as $column) {
            if ($column != $this->primaryKey) {
                $sql .= $column . ' = :' . $column;
                if (end($columns) != $column) {
                    $sql .= ', ';
                }
            }
        }
        $sql .= ' WHERE idTicket = ' . $idTicket;
        $this->db->exec('SET foreign_key_checks = 0');
        $insert = $this->db->prepare($sql);
        $result = $insert->execute($data);
        $this->db->exec('SET foreign_key_checks = 1');
        return $result;
    }

    /**
     * Change state of ticket from close to re-open
     * @param int $idTicket The ID of the ticket to re-open
     * @return boolean TRUE on succes, else FALSE
     */
    public function reopenTicket($idTicket) {
        if ($idTicket != null && is_numeric($idTicket)) {
            $sql = 'UPDATE ';
            $sql .= $this->table;
            $sql .= ' SET closeDate = :closeDate, status = :status WHERE idTicket = :idTicket';
            $data = array(
                'closeDate' => null,
                'idTicket' => $idTicket,
                'status' => 9
            );
            $this->db->exec('SET foreign_key_checks = 0');
            $insert = $this->db->prepare($sql);
            $result = $insert->execute($data);
            $this->db->exec('SET foreign_key_checks = 1');
            return $result;
        }
    }

    /**
     * Insert ticket data in data bases
     * @param array $data associative array of data
     * @return boolean TRUE on succes, else FALSE
     */
    public function createTicket($data) {
        $sql = 'INSERT INTO ';
        $sql .= $this->table;
        $sql .= ' (' . implode(',', array_keys($data)) . ') VALUES (:' . implode(',:', array_keys($data)) . ')';

        $insert = $this->db->prepare($sql);

        return $insert->execute($data);
    }

    /**
     * Count number of ticket registered in database
     * @return int The  number of ticket find in database
     */
    public function countAllTickets() {
        $query = $this->select(array('COUNT(*)'))
                ->from(array($this->table))
                ->buildQuery();

        $result = $this->db->query($query);
        return $result->fetchColumn();
    }

    /**
     * Get the last id in databases
     * @return array Associative array of database value
     */
    public function getLastId() {
        $query = $this->select(array('idTicket'))
                        ->from(array($this->table))
                        ->orderBy(array('idTicket'), 'DESC')
                        ->limit(0, 1)->buildQuery();
        $result = $this->db->query($query);
        return $result->fetch();
    }

    /**
     * Get the first id in databases
     * @return array Associative array of database value
     */
    public function getFirstId() {
        $query = $this->select(array('idTicket'))
                        ->from(array($this->table))
                        ->orderBy(array('idTicket'))
                        ->limit(0, 1)->buildQuery();
        $result = $this->db->query($query);
        return $result->fetch();
    }

    /**
     * Get the previous id in databases from current
     * @param int $current the current id
     * @return array Associative array of database value
     */
    public function getPreviousId($current) {
        $query = $this->select(array('idTicket'))
                        ->from(array($this->table))
                        ->where(array('idTicket' . ' < :idTicket'))
                        ->orderBy(array('idTicket'), 'DESC')
                        ->limit(0, 1)->buildQuery();
        $result = $this->db->prepare($query);
        $result->execute(array(':idTicket' => $current));
        return $result->fetch();
    }

    /**
     * Get the next id in databases from current
     * @param int $current the current id
     * @return array Associative array of database value
     */
    public function getNextId($current) {
        $query = $this->select(array('idTicket'))
                        ->from(array($this->table))
                        ->where(array('idTicket' . ' > :idTicket'))
                        ->limit(0, 1)->buildQuery();
        $result = $this->db->prepare($query);
        $result->execute(array(':idTicket' => $current));
        return $result->fetch();
    }

    /**
     * 
     * 
     * @param type $idTicket
     * @return type
     */
    public function getTicketMessages($idTicket) {
        $sql = $this->select()
                ->from(array('message'))
                ->join('user', 'user.idUser = message.createdBy')
                ->where(array('ticket = :idTicket'))
                ->orderBy(array('createdDate'), 'DESC')
                ->buildQuery();
        $getTicketMessages = $this->db->prepare($sql);
        $getTicketMessages->execute(array(':idTicket' => $idTicket));

        return $getTicketMessages->fetchAll();
    }

    /**
     * 
     * 
     * @param type $message
     * @param type $ticket
     */
    public function response($message, $ticket) {

        $this->db->beginTransaction();

        try {
            $this->db->exec('SET foreign_key_checks = 0');
            $this->save($ticket);

            $message['changes'] = json_encode($message['changes']);
            $this->save($message, 'message', 'idMessage');

            $this->db->exec('SET foreign_key_checks = 1');
            return $this->db->commit();
        } catch (Exception $ex) {

            var_dump($ex);

            $this->db->rollBack();
            return FALSE;
        }
    }

}
