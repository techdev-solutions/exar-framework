<?php
/** load EXAR bootstrap file */
require_once dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'bootstrap.php';

/** add test classes to include path */
set_include_path(dirname(__FILE__) . DIRECTORY_SEPARATOR . PATH_SEPARATOR . get_include_path());

/** clean EXAR cache */
\Exar\Autoloader::cleanCache();