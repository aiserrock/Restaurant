<?php
session_start();
define('security_hash', '1');

require_once 'engine/tools.php';
require_once 'engine/Classes/DB.php';

$routes = require_once 'engine/routes.php';
$route = preg_replace("#/$#", "", $_GET['route']);

if (!isset($_SESSION['id'])) {
    if ($route !== 'login') {
        header('Location: /login');
        exit;
    }
}

if ($route == 'login' || $route == 'logout') {
    require_once "engine/pages/{$route}.php";
} else {
    require_once 'engine/templates/header.php';
    require_once get_route($route, $routes);
    require_once 'engine/templates/footer.php';
}








