<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ticket_Model extends CI_Model
{

    private $table_name = 'ticket';
    private $primary_key = 'idTicket';

    /**
     * 
     * @param array $data
     * @return type
     */
    public function save($data) {
        return $this->db->insert($this->table_name, $data);
    }

    /**
     * 
     * @return array
     */
    public function loadAll($offset = null, $limit = null) {
        $query = $this->db->select()
                ->from($this->table_name)
                ->join('user', 'user.idUser = ' . $this->table_name . '.openBy')
                ->limit($limit, $offset);

        return $query->get()->result();
    }

    /**
     * 
     * @param int $idUser
     * @return array
     */
    public function loadById($idTicket) {
        $query = $this->db->select()
                ->from($this->table_name)
                ->join('user', 'user.idUser = ' . $this->table_name . '.openBy')
                ->where($this->primary_key, $idTicket);

        return $query->get()->row();
    }

    /**
     * 
     * @param int $idUser
     * @return array
     */
    public function loadTicketsOpenBy($idUser) {
        $query = $this->db->select()
                ->from($this->table_name)
                ->where('openBy', $idUser);

        return $query->get()->result();
    }

    /**
     * 
     * @param int $idUser
     * @return array
     */
    public function loadTicketsAssignedTo($idUser) {
        $query = $this->db->select()
                ->from($this->table_name)
                ->where('assignedTo', $idUser);

        return $query->get()->result();
    }

    /**
     * 
     * @param array $data
     * @return array
     */
    public function loadTicketTypes() {
        return $this->db->get('tickettype')->result();
    }

}