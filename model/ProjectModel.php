<?php

require_once './model/BaseModel.php';

/**
 * The project model
 * 
 * @author Alann DRAGIN <a.dragin@insta.fr>
 * @author Fabien MORCHOISNE <f.morchoisne@insta.fr>
 * 
 */
class ProjectModel extends BaseModel
{
    
    /**
     * Construct
     * 
     */
    public function __construct() {
        parent::__construct('project', 'idProject');
    }
    
    /**
     * Check if a project exists
     * 
     * @param string $name The project name
     * @return boolean
     */
    public function projectExists($name) {
        $sql = $this->select()
                ->where(array('name' . ' = :name'))
                ->buildQuery();

        $loadByName = $this->db->prepare($sql);
        $loadByName->execute(array(':name' => $name));
        $result = $loadByName->fetch();
        return !empty($result);
    }

}

