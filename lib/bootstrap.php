<?php
require_once 'vendor/autoload.php';

/** add EXAR sources to include path */
$exarSource = dirname(dirname(__FILE__)) . '/lib/';
set_include_path($exarSource . PATH_SEPARATOR . get_include_path());

/** Load Exar autoloader and register namespaces */
require_once __DIR__ . '/Exar/Autoloader.php';
Exar\Autoloader::register(dirname(dirname(__FILE__)) . '/_cache', array('Exar'));
