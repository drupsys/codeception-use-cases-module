<?php

// Check if we are already loaded
if (!defined('CODECEPTION_TEST')) {
    // Configure php error reporting level
    error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE ^ E_STRICT ^ E_DEPRECATED);

    // Make sure that we have the time zone set
    if (!ini_get('date.timezone')) {
        date_default_timezone_set('UTC');
    }

    // Overwrite the behavior of some of the functions found in the code / Common.php file
    function show_error($message, $status_code = 500, $heading = 'An Error Was Encountered') {
        throw new PHPUnit_Framework_Exception($message, $status_code);
    }
    function show_404($page = '', $log_error = true) {
        throw new PHPUnit_Framework_Exception("File not not found - $page", 404);
    }

    // Set testing env
    define('CODECEPTION_TEST', true);

    // Load CodeIgniter
    ob_start();
    require_once dirname(__FILE__) . '/instance.php';
    ob_end_clean();
}
