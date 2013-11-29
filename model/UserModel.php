<?php

require_once './model/BaseModel.php';

/**
 * The user model
 * 
 * @author Fabien MORCHOISNE <f.morchoisne@insta.fr>
 * @author Alex Maxime CADEVALL <a.cadevall@insta.fr>
 */
class UserModel extends BaseModel
{

    /**
     * 
     */
    public function __construct() {
        parent::__construct('user', 'idUser');
    }

    /**
     * 
     * @param type $login
     * @param type $password
     */
    public function authentificate($email, $password) {
        $sql = $this->select(array('idUser', 'email', 'firstname', 'lastname', 'roles', 'state'))
                ->where(array('email = :email', 'password = :password'))
                ->buildQuery();
        $auth = $this->db->prepare($sql);
        $auth->execute(array(':email' => $email, ':password' => $password));

        return $auth->fetch();
    }

    /**
     * 
     * @param type $email
     * @param type $token
     * @return type
     */
    public function activate($email, $token) {
        $sql = $this->select()
                ->where(array('email = :email', 'token = :token', 'state = :state'))
                ->buildQuery();
        $auth = $this->db->prepare($sql);
        $auth->execute(array(':email' => $email, ':token' => $token, ':state' => 0));

        $user = $auth->fetch();

        if (!empty($user)) {
            $user['state'] = 1;
            return $this->save($user);
        } else {
            return FALSE;
        }
    }

    /**
     * 
     * @param type $email
     * @return type
     */
    public function existEmail($email) {

        $sql = $this->select(array('email'))
                ->where(array('email = :email'))
                ->buildQuery();
        $existeEmail = $this->db->prepare($sql);
        $existeEmail->execute(array(':email' => $email));

        return $existeEmail->fetch();
    }
	public function loadUserData($idUser) {
        $sql = $this->select(array('idUser', 'email', 'firstname', 'lastname', 'roles', 'state'))
                ->where(array('idUser = :idUser'))
                ->buildQuery();
        $auth = $this->db->prepare($sql);
        $auth->execute(array(':idUser' => $idUser));

        return $auth->fetch();
    }
	
	/**
	 * 
	 * @return array
	 */
	public function loadUserList() {
		$query = $this->select()->from(array('user'))->buildQuery();
		$result = $this->db->prepare($query);
		$result->execute();
		return $result->fetchAll();
	}
}
