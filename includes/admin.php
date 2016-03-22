<?php
require_once ("database_object.php");
/**
 *
 */
class Admin extends DatabaseObject {

    protected static $table_name = "admin";
    protected static $db_fields = array('id', 'first_name', 'last_name', 'password');

}

?>