<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Twig
{

    private $CI;
    private $_twig;
    private $_template_dir;
    private $_cache_dir;
    private $_CI_functions = array(
        'base_url',
        'site_url',
        'current_url',
        'form_open',
        'form_hidden',
        'form_input',
        'form_password',
        'form_upload',
        'form_textarea',
        'form_dropdown',
        'form_multiselect',
        'form_fieldset',
        'form_fieldset_close',
        'form_checkbox',
        'form_radio',
        'form_submit',
        'form_label',
        'form_reset',
        'form_button',
        'form_close',
        'form_prep',
        'set_value',
        'set_select',
        'set_checkbox',
        'set_radio',
        'form_open_multipart'
    );

    function __construct($debug = false) {

        $this->CI = & get_instance();
        $this->CI->config->load('twig');

        log_message('debug', "Twig Autoloader Loaded");

        Twig_Autoloader::register();

        $template_global_dir = $this->CI->config->item('template_dir');
        $this->_template_dir = array($template_global_dir);

        $this->_cache_dir = $this->CI->config->item('cache_dir');

        $loader = new Twig_Loader_Filesystem($this->_template_dir);

        $this->_twig = new Twig_Environment($loader, array(
            'cache' => $this->_cache_dir,
            'debug' => $debug,
        ));

        foreach (get_defined_functions() as $functions) {
            foreach ($functions as $function) {
                $this->_twig->addFunction($function, new Twig_Function_Function($function));
            }
        }
    }

    public function render($template, $data = array()) {
        $template = $this->_twig->loadTemplate($template);
        return $template->render($data);
    }

    public function display($template, $data = array()) {
        $template = $this->_twig->loadTemplate($template);
        $template->display($data);
    }

    public function register_function($name, Twig_FunctionInterface $function) {
        $this->_twig_env->addFunction($name, $function);
    }

    public function ci_function_init() {
        foreach ($this->_CI_functions as $function) {
            $this->_twig->addFunction($function, new Twig_Function_Function($function));
        }

        $CI = $this->CI;
        $isGranted = new Twig_SimpleFunction('isGranted', function($role) use (&$CI) {
            $CI->load->model('user_model', 'user');
            return $CI->user->isGranted($role);
        });

        $this->_twig->addFunction($isGranted);
    }

}
