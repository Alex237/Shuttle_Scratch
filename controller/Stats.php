<?php
require_once './controller/BaseController.php';

/**
 * The dasboard controller
 * 
 * @author Alex Maxime CADEVALL <a.cadevall@insta.fr>
 */
class Stats extends BaseController{

	/*
	 * Constructor
	 */
	public function __construct(){
		parent::__construct();
                Session::run();
	}

	/*
	 * index view
	 */
	public function index() {
		$this->twig->display('stats/overview.html.twig');
	}

	/*
	 * show view
	 */
	public function show($idStats) {
		$this->twig->display('stats/show.html.twig', $data);
	}

	/*
	 * add view
	 */
	public function add() {
		//do somthing
	}

	/*
	 * delete view
	 */
	public function delete($idStats) {
		//do somthing
	}

	/*
	 * edit view
	 */
	public function edit($idStats) {
		//do somthing
	}

}
