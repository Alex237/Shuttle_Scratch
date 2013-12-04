<?php

/**
 * The user model
 * 
 * @author Alex Maxime CADEVALL <a.cadevall@insta.fr>
 */
require_once './model/BaseModel.php';

class TicketTypesModel extends BaseModel {

	/**
	 * 
	 */
	public function __construct() {
		parent::__construct('ticket', 'idTicket');
	}

	//private $table_name = 'ticket';
	//private $primary_key = 'idTicket';

	/**
	 * 
	 * @param array $data
	 * @return type
	 
	public function save($data) {
		//return $this->db->insert($this->table_name, $data);
	}

	/**
	 * 
	 * @return array
	 
	public function loadAll($offset = null, $limit = null) {
		/* $query = $this->db->select()
		  ->from($this->table_name)
		  ->join('user', 'user.idUser = ' . $this->table_name . '.openBy')
		  ->limit($limit, $offset);

		  if ($limit != null) {
		  $this->db->limit($limit, $offset);
		  }

		  return $query->get()->result(); 
	}

	/**
	 * 
	 * @param int $idUser
	 * @return array
	 
	public function loadById($idTicket) {
		/* $query = $this->db->select()
		  ->from($this->table_name)
		  ->join('user', 'user.idUser = ' . $this->table_name . '.openBy')
		  ->where($this->primary_key, $idTicket);

		  return $query->get()->row(); 
	}

	/**
	 * 
	 * @param int $idUser
	 * @return array
	 
	public function loadTicketsOpenBy($idUser) {
		/* $query = $this->db->select()
		  ->from($this->table_name)
		  ->where('openBy', $idUser);

		  return $query->get()->result(); 
	}

	/**
	 * 
	 * @param int $idUser
	 * @return array
	 
	public function loadTicketsAssignedTo($idUser) {
		/* $query = $this->db->select()
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
		$query = $this->select()->from(array('tickettype'))->buildQuery();
		$result = $this->db->prepare($query);
		$result->execute();
		return $result->fetchAll();
	}

	/**
	 * 
	 * @param array $data
	 * @return array
	 */
	public function loadProjectList() {
		$query = $this->select()->from(array('project'))->buildQuery();
		$result = $this->db->prepare($query);
		$result->execute();
		return $result->fetchAll();
	}

}
