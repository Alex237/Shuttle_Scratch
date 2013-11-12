<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Project_Model extends CI_Model
{

    private $table_name = 'project';
    private $primary_key = 'idProject';


    /**
     * 
     * @param array $data
     * @return type
     */
    public function save($data) {
        return $this->db->insert($this->table_name, $data);
    }

}