<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
  | -------------------------------------------------------------------------
  | URI ROUTING
  | -------------------------------------------------------------------------
  | This file lets you re-map URI requests to specific controller functions.
  |
  | Typically there is a one-to-one relationship between a URL string
  | and its corresponding controller class/method. The segments in a
  | URL normally follow this pattern:
  |
  |	example.com/class/method/id/
  |
  | In some instances, however, you may want to remap this relationship
  | so that a different class/function is called than the one
  | corresponding to the URL.
  |
  | Please see the user guide for complete details:
  |
  |	http://codeigniter.com/user_guide/general/routing.html
 */

$route['login'] = 'user/login';
$route['register'] = 'user/register';
$route['logout'] = 'user/logout';
$route['settings'] = 'user/settings';

$route['dashboard'] = 'dashboard/overview';

$route['users'] = 'user/index';
$route['user/new'] = 'user/create';
$route['user/(\d+)/show'] = 'user/show/$1';
$route['user/(\d+)/edit'] = 'user/edit/$1';
$route['user/(\d+)/delete'] = 'user/delete/$1';

$route['companies'] = 'company/index';
$route['company/new'] = 'company/create';
$route['company/(\d+)/show'] = 'company/show/$1';
$route['company/(\d+)/edit'] = 'company/edit/$1';
$route['company/(\d+)/delete'] = 'company/delete/$1';

$route['tickets'] = 'ticket/index';
$route['ticket/new'] = 'ticket/create';
$route['ticket/(\d+)/show'] = 'ticket/show/$1';
$route['ticket/(\d+)/close'] = 'ticket/close/$1';

$route['projects'] = 'project/index';
$route['project/new'] = 'project/create';
$route['project/(\d+)/show'] = 'project/show/$1';
$route['project/(\d+)/edit'] = 'project/edit/$1';

/*
  | -------------------------------------------------------------------------
  | RESERVED ROUTES
  | -------------------------------------------------------------------------
  |
  | There area two reserved routes:
  |
  |	$route['default_controller'] = 'welcome';
  |
  | This route indicates which controller class should be loaded if the
  | URI contains no data. In the above example, the "welcome" class
  | would be loaded.
  |
  |	$route['404_override'] = 'errors/page_missing';
  |
  | This route will tell the Router what URI segments to use if those provided
  | in the URL cannot be matched to a valid route.
  |
 */

$route['default_controller'] = "user/login";
$route['404_override'] = 'errors/404';


/* End of file routes.php */
/* Location: ./application/config/routes.php */