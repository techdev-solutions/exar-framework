<?php
/** load EXAR bootstrap file */
require_once dirname(dirname(__FILE__)) .'/lib/bootstrap.php';

/** add test classes to include path */
set_include_path(dirname(__FILE__) . '/' . PATH_SEPARATOR . get_include_path());

/** clean EXAR cache */
\Exar\Autoloader::cleanCache();