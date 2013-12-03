<?php
require_once './controller/BaseController.php';

/**
 * The dasboard controller
 * 
 * @author Alex Maxime CADEVALL <a.cadevall@insta.fr>
 */
class Settings extends BaseController
{

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        Session::run();
    }

    /**
     * The controller index
     */
    public function index() {
        $this->twig->display('settings/overview.html.twig');
    }

    public function add() {
        //to do
    }

    public function delete($id) {
        //to do
    }

    public function edit($id) {
        //to do
    }

    public function show($id) {
        //to do
    }

}
