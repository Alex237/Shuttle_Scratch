<?php

require_once './vendor/Twig/Autoloader.php';
require_once './core/session.php';

/**
 * The base controller
 * 
 * @author Fabien MORCHOISNE <f.morchoisne@insta.fr>
 */
abstract class BaseController
{

    /**
     * The twig environment
     * 
     * @var \Twig_Environment
     */
    protected $twig;

    /**
     * The main controller model
     *
     * @var \BaseModel
     */
    protected $model = null;

    /**
     * The active session
     * 
     * @var \Session
     */
    protected $session;

    /**
     * The alerts messages
     * 
     * @var array
     */
    protected $alerts;

    /**
     * Construct
     * 
     * 0/ Open a session
     * 1/ Load the default model if specified
     * 2/ Build Twig environment
     * 3/ Add flash messages to environment
     * 4/ Extends Twig with personal fonctions
     * 
     * @param string $model The default entity model
     */
    protected function __construct($model = null) {

        $this->session = new Session();

        if (null != $model) {
            require_once './model/' . $model . '.php';
            $this->model = new $model();
        }

        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem('./view');
        $this->twig = new Twig_Environment($loader, array('debug' => true));
        $this->twig->addExtension(new Twig_Extension_Debug());
        $this->twig->addGlobal('alerts', $this->session->getFlash());
        $this->extend();
    }

    /**
     * The controller index
     * 
     */
    abstract public function index();

    /**
     * Add an entity
     * 
     */
    abstract public function add();

    /**
     * Display an entity from its id
     * 
     * @param int $id The entity id
     */
    abstract public function show($id);

    /**
     * Edit an entity from its id
     * 
     * @param int $id The entity id
     */
    abstract public function edit($id);

    /**
     * Delete an entity from its id
     * 
     * @param int $id The entity id
     * @return void
     */
    abstract public function delete($id);

    /**
     * Perform a inner-domain header redirection
     * This can not redirect cross-domain
     * 
     * @param string $request The inner-domain request uri
     */
    protected function redirect($request) {
        if (substr($request, 0, 7) == 'http://') {
            header('location: ' . $request);
        } else {
            header('location: http://' . $_SERVER['SERVER_NAME'] . $request);
        }

        exit;
    }

    /**
     * Restrict access to a logged user with specific role
     * 
     * 0/ Check if the required role is granted
     * 1/ Add a flash message
     * 2/ Redirect to the http_referer
     * 
     * @return void
     */
    protected function restrict($role = null) {
        if (!$this->session->isGranted($role)) {
            if (!$this->session->existSessionData()) {
                 header('location: http://' . $_SERVER['SERVER_NAME'] . '/login');
                 exit;
            }
            $this->session->addFlash('<div class="alert alert-danger"><i class="fa-minus-circle"></i>&nbsp;Cette action est interdite !</div>', 'danger');
            $this->redirect($_SERVER['HTTP_REFERER']);
        }
    }

    /**
     * Create an alert message and allow Twig to access them
     * 
     * 0/ Look for the icon to use
     * 1/ Build the alert message
     * 2/ Update the Twig environment
     * 
     * @param string $message The alert message
     * @param string $type The alert type
     * @return string
     */
    protected function alert($message, $type = 'info') {
        switch ($type) {
            case 'success' :
                $icon = 'fa-check-circle';
                break;
            case 'warning' :
                $icon = 'fa-exclamation-triangle';
                break;
            case 'danger' :
                $icon = 'fa-minus-circle';
                break;
            default :
                $icon = 'fa-exclamation-circle';
                $type = 'info';
        }

        $this->alerts[$type] = '<div class="alert alert-' . $type . '"><i class="' . $icon . '"></i>&nbsp;' . $message . '</div>';
        $this->twig->addGlobal('alerts', $this->alerts);
    }

    /**
     * 
     * @return void
     */
    private function extend() {
        $functions[] = new Twig_SimpleFunction('setValue', function ($input, $default = null) {
                    if (isset($_POST[$input])) {
                        echo 'value="' . $_POST[$input] . '"';
                    } elseif (!is_null($default)) {
                        echo 'value="' . $default . '"';
                    }
                });

        $functions[] = new Twig_SimpleFunction('domain', function () {
                    echo 'http://' . $_SERVER['SERVER_NAME'] . '/';
                });

        $functions[] = new Twig_SimpleFunction('formError', function ($field, $html = true) {
                    if (class_exists('Validator')) {
                        $validator = Validator::getInstance();
                        if (($message = $validator->getError($field)) != FALSE) {
                            if ($html) {
                                echo '<div class="input-error">' . $message . '</div>';
                            } else {
                                echo $message;
                            }
                        }
                    }
                });

        $functions[] = new Twig_SimpleFunction('hasError', function ($field) {
                    if (class_exists('Validator')) {
                        $validator = Validator::getInstance();
                        return $validator->hasError($field);
                    }
                });

        $functions[] = new Twig_SimpleFunction('set_select', function($select, $value, $default = false) {
                    if ((isset($_POST[$select]) and $_POST[$select] == $value) or $default) {
                        echo 'selected="selected"';
                    }
                });

        $functions[] = new Twig_SimpleFunction('isGranted', function($roles) {
                    $sessiondata = $this->session->getUserData();
					return in_array($roles, $sessiondata["roles"]);
                });

        foreach ($functions as $function) {
            $this->twig->addFunction($function);
        }
    }

}
