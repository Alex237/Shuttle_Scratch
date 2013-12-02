<?php

/**
 * The user model
 * 
 * @author Alex Maxime CADEVALL <a.cadevall@insta.fr>
 */
require_once './model/BaseModel.php';

class TicketsModel extends BaseModel {

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct('ticket', 'idTicket');
    }

    /**
     * 
     * @param array $data
     * @return boolean
     */
    public function submit($data) {
        return $this->save($data);
    }

    /**
     * 
     * @param type $offset
     * @param type $limit
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
     * 
     * @param int $idUser
     * @return array
     */
    public function loadById($idTicket, $columns = null) {
        $query = $this->select(array(
                            $this->table . '.*',
                            'u.*',
                            'tt.*',
                            'ts.*',
                            'u2.firstname as assignedToFirstname',
                            'u2.lastname as assignedToLastname'
                        ))
                        ->from(array($this->table))
                        ->join('user u', 'u.idUser = ' . $this->table . '.openBy')
                        ->join('tickettype tt', 'tt.idTicketType = ' . $this->table . '.type')
                        ->join('ticketstatus ts', 'ts.idStatus = ' . $this->table . '.status')
                        ->join('user u2', 'u2.idUser = ' . $this->table . '.assignedTo')
                        ->where(array($this->primaryKey . ' = :idTicket'))->buildQuery();
        $result = $this->db->prepare($query);
        $result->execute(array(':idTicket' => $idTicket));
        return $result->fetch();
    }

    /**
     * 
     * @param int $idUser
     * @return array
     */
    public function loadTicketsOpenBy($idUser) {
        /* $query = $this->db->select()
          ->from($this->table_name)
          ->where('openBy', $idUser);

          return $query->get()->result(); */
    }

    /**
     * 
     * @param int $idUser
     * @return array
     */
    public function loadTicketsAssignedTo($idUser) {
        /* $query = $this->db->select()
          ->from($this->table_name)
          ->where('assignedTo', $idUser);

          return $query->get()->result(); */
    }

    /**
     * 
     * @return type
     */
    public function loadTicketTypes() {
        $query = $this->select()->from(array('tickettype'))->buildQuery();
        $result = $this->db->prepare($query);
        $result->execute();
        return $result->fetchAll();
    }

    /**
     * 
     * @return type
     */
    public function loadProjectList() {
        $query = $this->select()->from(array('project'))->buildQuery();
        $result = $this->db->prepare($query);
        $result->execute();
        return $result->fetchAll();
    }

    /**
     * 
     * @return type
     */
    public function loadMemberList() {
        $query = $this->select(array('idUser', 'firstname', 'lastname', 'roles'))
                ->from(array('user'))
                ->buildQuery();
        $result = $this->db->prepare($query);
        $result->execute();
        return $result->fetchAll();
    }

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

    public function createTicket($data) {
        $sql = 'INSERT INTO ';
        $sql .= $this->table;
        $sql .= ' (' . implode(',', array_keys($data)) . ') VALUES (:' . implode(',:', array_keys($data)) . ')';

        $insert = $this->db->prepare($sql);

        return $insert->execute($data);
    }/**
     * 
     * @param type $offset
     * @param type $limit
     */
    public function countAllTickets() {
        $query = $this->select(array('COUNT(*)'))
                        ->from(array($this->table))
                        ->buildQuery();

        $result = $this->db->query($query);
        return $result->fetchColumn();
    }
    
    public function getLastId() {
        $query = $this->select(array('idTicket'))
                ->from(array($this->table))
                ->orderBy(array('idTicket'), 'DESC')
                ->limit(0, 1)->buildQuery();//SELECT fields FROM table ORDER BY id DESC LIMIT 1;
        $result = $this->db->query($query);
        return $result->fetch();
        
    }
    public function getFirstId() {
        $query = $this->select(array('idTicket'))
                ->from(array($this->table))
                ->orderBy(array('idTicket'))
                ->limit(0, 1)->buildQuery();//SELECT fields FROM table ORDER BY id DESC LIMIT 1;
        $result = $this->db->query($query);
        return $result->fetch();
        
    }

}
