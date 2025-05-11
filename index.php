<?php
// Main entry point for the application
session_start();
require_once 'config/config.php';
require_once 'controllers/Router.php';

// Initialize the router
$router = new Router();
$router->route();
?>