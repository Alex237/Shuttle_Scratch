<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Company_Model extends CI_Model
{

    private $table_name = 'company';
    private $primary_key = 'idCompany';

    /**
     * 
     * @return array
     */
    public function loadAll($offset = null, $limit = null) {
        $query = $this->db->select()
                ->from($this->table_name)
                ->limit($limit, $offset);
        
        if($limit != null) {
            $this->db->limit($limit, $offset);
        }

        return $query->get()->result();
    }
    
    /**
     * 
     * @param array $data
     * @return type
     */
    public function save($data) {
        return $this->db->insert($this->table_name, $data);
    }

}