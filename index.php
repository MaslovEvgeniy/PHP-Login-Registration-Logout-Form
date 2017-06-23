<?php
error_reporting (E_ALL);

session_start();

define('ROOT', dirname(__FILE__));

define('URL', '');//provide your path here

require_once(ROOT . '/vendor/autoload.php');

$router = new app\components\Router;
$router->run();