<?php
require_once ("database_object.php");
/**
 *
 */
class Admin extends DatabaseObject {

    protected static $table_name = "admin";
    protected static $db_fields = array('id', 'first_name', 'last_name', 'password');
    public $id;
    public $password;
    public $first_name;
    public $last_name;
    public $errors=array();
    protected $upload_errors = array(
        UPLOAD_ERR_OK           => "No errors.",
        UPLOAD_ERR_INI_SIZE     => "Larger than upload_max_filesize.",
        UPLOAD_ERR_FORM_SIZE    => "Larger than MAX_FILE_SIZE",
        UPLOAD_ERR_PARTIAL      => "Patal upload.",
        UPLOAD_ERR_NO_FILE      => "No file.",
        UPLOAD_ERR_NO_TMP_DIR   => "No temporary directory.",
        UPLOAD_ERR_CANT_WRITE   => "Can't write to disk.",
        UPLOAD_ERR_EXTENSION    => "File upload stopped by extension"
    );

}

?>