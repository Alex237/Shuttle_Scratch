<?php

require_once './controller/BaseController.php';
require_once './core/validator.php';
require_once './core/mailer.php';

/**
 * The user controller
 * 
 * @author Fabien MORCHOISNE <f.morchoisne@insta.fr>
 */
class User extends BaseController
{

    /**
     * Construct
     * 
     */
    public function __construct() {
        parent::__construct('UserModel');
    }

    /**
     * Index
     * 
     */
    public function index() {

        $this->restrict('team');
        $this->model->init();

        $this->twig->display('user/user.html.twig', array(
            'users' => $this->model->loadAll()
        ));

        $this->model->close();
    }

    /**
     * Login
     * 
     */
    public function login() {

        if ($_SERVER['REQUEST_METHOD'] == 'POST' and !empty($_POST)) {

            $this->validator = new Validator();
            $this->validator->addRules('email', 'required|email')
                    ->addRules('password', 'required');

            if ($this->validator->run()) {

                $email = strtolower($_POST['email']);
                $password = md5($_POST['password'] . $email);

                $this->model->init();
                if (($user = $this->model->authentificate($email, $password)) != FALSE) {

                    if ($user['state'] == 1) {

                        $user['roles'] = json_decode($user['roles']);

                        $this->session->startUserSession($user);

                        $this->model->close();
                        $this->redirect('/dashboard');
                    } elseif ($user['state'] == 0) {
                        $this->validator->addCustomError('credentials', 'Accèder à votre boîte mail pour activer ce compte');
                    } elseif ($user['state'] >= 2) {
                        $this->validator->addCustomError('credentials', 'Ce compte n\'est plus autorisé à se connecter');
                    }
                } else {
                    $this->validator->addCustomError('credentials', 'Identifiants incorrects');
                }
            }
        }

        $this->twig->display('user/login.html.twig');

        $this->model->close();
    }

    /**
     * Display an user from its id
     * 
     * @param int $idUser The user id
     */
    public function show($idUser) {

        $this->restrict('team');
        $this->model->init();

        $user = $this->model->loadById($idUser);
        if (empty($user)) {
            $this->redirect('/user');
        }

        $this->twig->display('user/show.html.twig', array(
            'user' => $user
        ));

        $this->model->close();
    }

    /**
     * Edit an user from its id
     * 
     * @param int $idUser The usr id
     */
    public function edit($idUser) {

        $this->restrict('admin');

        $this->model->init();

        $user = $this->model->loadById($idUser);
        if (empty($user)) {
            $this->redirect('/user');
        }

        // test d'égalité uri<->form pour plus de sécurité
        if ($_SERVER['REQUEST_METHOD'] == 'POST' and $_POST['idUser'] == $idUser) {

            $this->validator = new Validator();
            $this->validator->addRules('firstname', 'maxlength[45]')
                    ->addRules('lastname', 'maxlength[45]');

            if ($this->validator->run()) {

                $data = array(
                    'idUser' => $idUser,
                    'firstname' => ucwords(strtolower($_POST['firstname'])),
                    'lastname' => strtoupper($_POST['lastname']),
                    'roles' => json_encode($_POST['roles']),
                );

                if ($this->model->save($data)) {
                    $this->alert('Utilisateur #' . $idUser . ' modifié', 'success');
                } else {
                    $this->alert('Impossible de sauvegarder les changements', 'danger');
                }
            }
        }

        $this->twig->display('user/edit.html.twig', array(
            'user' => $user
        ));

        $this->model->close();
    }

    /**
     * Delete an user from its id
     * 
     * @param int $idUser The entity id
     */
    public function delete($idUser) {

        $this->restrict('admin');
        $this->model->init();
        if ($this->model->deleteById($idUser)) {
            $this->session->addFlash('Utilisateur désactivé', 'success');
            $this->redirect('/user');
        } else {
            $this->session->addFlash('Impossible de supprimer cet utilisateur', 'danger');
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
    }

    /**
     * Add a new user
     * 
     */
    public function add() {

        $this->restrict('admin');

        if ($_SERVER['REQUEST_METHOD'] == 'POST' and !empty($_POST)) {

            $this->validator = new Validator();
            $this->validator->addRules('firstname', 'maxlength[45]')
                    ->addRules('lastname', 'maxlength[45]')
                    ->addRules('email', 'required|email|maxlength[255]');

            if ($this->validator->run()) {

                $email = strtolower($_POST['email']);
                $this->model->init();
                $user = $this->model->loadByEmail($email);

                if (!empty($user)) {
                    $this->validator->addCustomError('email', 'Cette adresse email est déjà allouée');
                } else {

                    $now = new \DateTime();
                    $password = $this->model->generatePassword();
                    $token = md5(uniqid($email));
                    $user = array(
                        'email' => $email,
                        'password' => md5($password . $email),
                        'token' => $token,
                        'firstname' => ucwords(strtolower($_POST['firstname'])),
                        'lastname' => strtoupper($_POST['lastname']),
                        'roles' => isset($_POST['roles']) ? json_encode($_POST['roles']) : array(),
                        'registerDate' => $now->format('Y-m-d H:i:s')
                    );

                    if ($this->model->save($user)) {

                        $mailer = new Mailer();
                        $user['password'] = $password;
                        $mailer->mailUserCreate($user, $this->twig);

                        $this->session->addFlash('Utilisateur ajouté', 'success');
                        $this->redirect('/user');
                    } else {
                        $this->alert('Impossible d\'ajouter un utilisateur', 'danger');
                    }
                }

                $this->model->close();
            }
        }

        $this->twig->display('user/add.html.twig');
    }

    /**
     * Logout the user
     * 
     */
    public function logout() {

        $this->session->endUserSession();
        $this->redirect('/login');
    }

    /**
     * User registration
     * 
     */
    public function register() {

        if ($_SERVER['REQUEST_METHOD'] == 'POST' and !empty($_POST)) {

            $this->validator = new Validator();
            $this->validator->addRules('email', 'required|email|maxlength[255]')
                    ->addRules('password', 'required|maxlength[24]')
                    ->addRules('confirm', 'match[password]');

            if ($this->validator->run()) {

                $this->model->init();

                $email = strtolower($_POST['email']);
                $user = $this->model->loadByEmail($email);

                if (!empty($user)) {

                    $this->validator->addCustomError('email', 'Cette adresse email est déjà allouée');
                } else {

                    $password = md5($_POST['password'] . $email);
                    $token = md5(uniqid($email));
                    $now = new \DateTime();

                    $user = array(
                        'email' => $email,
                        'password' => $password,
                        'token' => $token,
                        'roles' => array(),
                        'registerDate' => $now->format('Y-m-d H:i:s')
                    );

                    if ($this->model->save($user)) {

                        $mailer = new Mailer();
                        $mailer->mailUserRegister($user, $this->twig);

                        $this->twig->display('info/registerSuccess.html.twig', array(
                            'email' => $email
                        ));
                    } else {
                        $this->twig->display('info/registerFailure.html.twig');
                    }

                    exit;
                }
            }
        }

        $this->twig->display('user/register.html.twig');
    }

    /**
     * Activate a user account from mail token
     * 
     * @param string $email The user email address
     * @param string $token The user mail token
     */
    public function activate($email, $token) {
        $this->model->init();

        if ($this->model->activate($email, $token)) {

            $this->twig->display('info/msg.success.request.twig', array(
                'view' => 'login',
                'msg' => 'Votre compte a bien été activé.'
            ));
        } else {

            $this->twig->display('info/msg.failure.request.twig', array(
                'view' => 'login',
                'msg' => 'Impossible d\'activer ce compte.'
            ));
        }

        $this->model->close();
    }

    /**
     * Allow user to recover password
     * 
     * @param string $email The user email address
     * @param string $token The user mail token
     */
    public function recover() {
        
    }

}
