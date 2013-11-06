<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ticket extends CI_Controller
{

    /**
     * 
     */
    public function index() {
        $this->load->view('layout', array('content' => 'ticket/home'));
    }

    /**
     * 
     */
    public function add() {

        /*
         * TODO 
         * 
         * affichage formulaire
         * 
         * si post traitement formulaire
         * -> si bon redirect
         * -> si mauvais re-affichage formulaire + erreurs (form_valid)
         * 
         */

        $this->load->view('layout', array('content' => 'ticket-add'));
    }

}