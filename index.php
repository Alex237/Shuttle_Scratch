<?php

header('Content-type: text/html; charset=UTF-8');

require_once './core/router.php';
$router = Router::getInstance();
$router->run();
