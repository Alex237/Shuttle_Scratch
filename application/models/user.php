<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class User extends CI_Model
{

    private $table_name = 'user';
    private $primary_key = 'idUser';

    /**
     * 
     * @return type
     */
    function online($role = null) {
        if ($role != null) {
            // handle roles
        } else {
            return $this->session->userdata($this->primary_key);
        }
    }

    /**
     * 
     * @param type $email
     * @param type $password
     * @return boolean
     */
    public function authentification($email, $password) {

        $this->db->select()
                ->from($this->table_name)
                ->where('email', $email)
                ->where('password', $password);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $userdata = $query->row();

            $this->session->set_userdata($userdata);
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
        $now = new \DateTime();
        $this->db->where($this->primary_key, $this->session->userdata($this->primary_key));
        $this->db->update($this->table_name, array('lastloginDate' => $now->format('Y-m-d H:i:s')));
        $this->session->sess_destroy();
    }

    /**
     * 
     * @param array $data
     * @return type
     */
    public function save($data) {
        return $this->db->insert('user', $data);
    }

    /**
     * 
     * @param string $email
     * @return type
     */
    public function isUniqueEmail($email) {
        $result = $this->db->get_where('user', array('email' => $email));
        return ($result->num_rows == 0);
    }

}