<?php
// (/) DIRECTORY_SEPARATOR
defined('DS')? NULL : define('DS', DIRECTORY_SEPARATOR);

defined('SITE_ROOT')? NULL : define('SITE_ROOT', 'C:'.DS.'wamp'.DS.'www'.DS.'fcit_erm');

// library path, includes file
defined('LIB_PATH')? NULL : define('LIB_PATH', SITE_ROOT.DS.'includes');


/*** the order is important, HOW? I don't know***/


// first load the config.php first
require_once(LIB_PATH.DS."config.php");


// load basic functions first, so that eth after can use them
require_once(LIB_PATH.DS. "functions.php");


// Load core objects
require_once(LIB_PATH.DS. "session.php");
require_once(LIB_PATH.DS. "database.php");
require_once(LIB_PATH.DS. "database_object.php");
// require_once(LIB_PATH.DS. "pagination.php");


//Load database related classes
require_once(LIB_PATH.DS. "admin.php");
require_once(LIB_PATH.DS. "member.php");


?>