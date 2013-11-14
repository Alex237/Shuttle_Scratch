<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User_Model extends CI_Model
{

    private $table_name = 'user';
    private $primary_key = 'idUser';

    /**
     * 
     * @return boolean
     */
    function isLogged() {

        return $this->session->userdata($this->primary_key);
    }

    /**
     * 
     * @param string $role
     * @return boolean
     */
    public function isGranted($role) {

        return in_array($role, $this->session->userdata('roles'));
    }

    /**
     * 
     * @param string $email
     * @param string $password
     * @return boolean
     */
    public function authentification($email, $password) {

        $this->db->select()
                ->from($this->table_name)
                ->where('email', $email)
                ->where('password', $password);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $user = $query->row();
            $userdata = array(
                'idUser' => $user->idUser,
                'email' => $user->email,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname,
                'roles' => json_decode($user->roles)
            );

            $this->session->set_userdata($userdata);
            $this->updateLoginDate();
            return true;
        } else {
            return false;
        }
    }

    /**
     * 
     * @return type
     */
    function logout() {

        $this->updateLoginDate();
        $this->session->sess_destroy();
    }

    /**
     * 
     * @return array
     */
    public function loadAll($offset = null, $limit = null) {

        $query = $this->db->select()
                ->from($this->table_name);

        if ($limit != null) {
            $this->db->limit($limit, $offset);
        }

        return $query->get()->result();
    }

    /**
     * 
     * @return array
     */
    public function loadById($idUser) {

        $query = $this->db->select()
                ->from($this->table_name)
                ->where($this->primary_key, $idUser);

        return $query->get()->row();
    }

    /**
     * 
     * @param array $data
     * @return type
     */
    public function save($data) {

        if (array_key_exists($this->primary_key, $data)) {
            return $this->db->update($this->table_name, $data, array($this->primary_key => $data[$this->primary_key]));
        }

        return $this->db->insert($this->table_name, $data);
    }

    /**
     * 
     * @param string $email
     * @return type
     */
    public function isUniqueEmail($email) {

        $result = $this->db->get_where($this->table_name, array('email' => $email));
        return ($result->num_rows == 0);
    }

    /**
     * 
     */
    public function updateLoginDate() {

        $now = new \DateTime();
        $this->db->where($this->primary_key, $this->session->userdata($this->primary_key));
        $this->db->update($this->table_name, array('lastloginDate' => $now->format('Y-m-d H:i:s')));
    }

}