<?php

/**
 * Created by PhpStorm.
 * User: windows
 * Date: 14/03/2016
 * Time: 04:15 am
 */

require_once ("database.php");

class Upload {

    protected static $table_name = "uploads";
    protected static $db_fields = array('id', 'filename', 'type',
        'size', 'caption', 'member_id');
    public $id;
    public $filename;
    public $type;
    public $size;
    public $caption;
    public $member_id;
    protected static $accepted_extentions = array('pdf','doc','docx','ppt','pptx');
    private $temp_path;
    protected $upload_dir="../uploads";
    public $errors=array(); // as we upload, save, move photos we can keep track and catalog the errors
    // and then return them; so we are not limited to the errors below
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

    function __construct()
    {

    }

    // Pass in $_FILES['uoloaded_file'] as an argument
    public function attach_file($file, $member_id){
        // Perform error checking on the form parameters
        if(!$file || empty($file) || !is_array($file)){
            // error: nothing uploaded or wrong argument usage
            $this->errors[] = "No file was uploaded.";
            return false;
        }elseif($file['error'] != 0){
            // error: report what PHP says went wrong
            $this->errors[] = $this->upload_errors[$file['error']];
            return false;
        }elseif (empty($member_id) || $member_id === "") {
            $this->errors[] = "Member id was not provided.";
            return false;
        }elseif(!in_array(pathinfo($file['name'], PATHINFO_EXTENSION), self::$accepted_extentions)){
            $this->errors[] = "Extention is not Accepted.";
            return false;
        }
        else{
            // Set object attributes to the form parameters.
            $this->temp_path = $file['tmp_name'];
            $this->filename = basename($file['name']);
            $this->type = $file['type'];
            $this->size = $file['size'];
            $this->member_id = $member_id;
            // don't worry about saving to the database yet.
            return true;
        }
    }

    public function save(){
        // A new record won't have an id yet.
        if(isset($this->id)){
            // really just to update the caption
            $this->update();
        } else {
            // *** Make sure there are no errors

            // Can't save if there are pre-existing errors
            if(!empty($this->errors)) {return false;}
            // make sure the caption is not too long
            if(strlen($this->caption) >= 255){
                $this->errors[] = "The caption can only be 255 characters long.";
                return false;
            }
            // Can't save without the filename and temp location
            if(empty($this->filename) || empty($this->temp_path)){
                $this->errors[] = "The file location was not available.";
                return false;
            }
            // Determine the target path
            $terget_path = "C:/wamp/www/fcit_erm/public/member/uploads/" . $this->filename;
            // Make sure the file is not already exists in  the target location
            if(file_exists($terget_path)){
                $this->errors[] = "The file {$this->filename} already exists.";
                return false;
            }
            // *** attemt to move the file

            if(move_uploaded_file($this->temp_path, $terget_path)){
                //Success
                // Save a corresponding entry to the database
                if($this->create()){
                    // we are done with temp_file, the file isn't there anymore
                    unset($this->temp_path);
                    return true;
                }
            }else{
                // Failure
                $this->errors = "The file upload failed, propably due to incorrect permission
                on the upload folder.";
            }
        }
    }

    // you could remove the database entry first, then this one
    // but if we removed the databse first, even if the file still setting there, but it will
    // not be longer in the website
    public function destroy(){
        // **first remove the database entry
        if($this->delete()){
            // remove the file
            $target_path = "C:/wamp/www/fcit_erm/public/member/uploads/" . $this->filename;
            return unlink($target_path) ? true : false;
        }else{
            // database delete failed
            return false;
        }
        //**second remove the file

    }

    //image_path() return image path

//    public function image_path(){
//        // (DS) directory separator not working???
//        return $this->upload_dir . "/" . $this->filename;
//    } ima  d

    public function size_as_text(){
        if($this->size < 1024){
            return "{$this->size}";
        }elseif($this->size < 1048576){
            $size_kb = round($this->size / 1024);
            return "{$size_kb} KB";
        }else{
            $size_mb = round($this->size / 1048576, 1);
            return "{$size_mb} MB";
        }
    }

    public function comments(){
        return Comment::find_comments_on($this->id);
    }

    // Common DB methods, copied as it is from user class, if we had DatabaseObject created
    // we could promote all these methods and inhirate them with no need to rewrite them again
    public static function find_all_uploads(){
        return self::find_by_sql("select * from " . self::$table_name);
    }

    public static function find_uploads_by_member_id($id=0){
        global $database;
        $result_array = self::find_by_sql("SELECT * FROM ". self::$table_name ." WHERE member_id =
        {$database->escape_value($id)}");
        // if $result_array empty, then return false, else get the item out of $result_array and return it
        return !empty($result_array)? $result_array : false;
    }

    public static function find_by_sql($sql=""){
        global $database;
        $result_set = $database->query($sql);
        $object_array = array();
        while($row = $database->fetch_array($result_set)){
            $object_array[] = self::instantiate($row);
        }
        return $object_array;
    }

    public static function count_all(){
        global $database;
        $sql= "SELECT COUNT(*) FROM " .self::$table_name;
        /* find_by_sql isn't gonna work for us cuz it does instantiate & return object
        we don't want that, we just want the count, so we're gonna run the query using $database*/
        $result_set = $database->query($sql);
        /* the query will return a record even though it was a single value, so we need to fetch
        // the first row from the $result_ser*/
        $row = $database->fetch_array($result_set);
        /* even though the record has a single value, but we need to pull it out since it
        // is the first value in the record*/
        return array_shift($row);

    }

    private static function instantiate($record){
        // it is good to check $record exists and is an array

        // this is a simple, long form approach to assign values
        $object = new self;
//        $object->id         = $record['id'];
//        $object->username   = $record['username'];
//        $object->password   = $record['password'];
//        $object->first_name = $record['first_name'];
//        $object->last_name  = $record['last_name'];


        // more dynamic, short form approach to assign values
        foreach($record as $attribute => $value){
            /* maybe the record in the DB has some extra fields that doesn't exists in the class
             so we do extra check */
            if($object->has_attribute($attribute)){
                $object->$attribute = $value;
            }
        }
        return $object;
    }

    private function has_attribute($attribute){
        $object_vars = get_object_vars($this);
        return array_key_exists($attribute, $object_vars);
    }

    protected function attributes(){
        // return an array of attribute keys and thier values
        return get_object_vars($this);
        /*get_object_vars() has two issues: 1- returns all the attributs that it has access to
        (called from inside a class, then returs private and protected)
        2- some attributes has no database fields, two reasons it is not good for our use
        to solve these two issues we will create get_attributes();*/
    }

    protected function get_attributes(){
        $attributes = array();
        foreach(self::$db_fields as $field){
            if(property_exists($this, $field)){
                $attributes[$field] = $this->$field; // $this->$field; field here is dynamic, don't let that confuse you
            }
        }
        return $attributes;
        /*this will work for our purpose, but if we have a big DB with hundred fields how we can do that?
        we can use SQL statement (SHOW FIELDS FROM users) then we buld our associative array ...*/
    }

    protected function sanitized_attributes(){
        global $database;
        //$attributes = get_object_vars($this);
        $cleaned_attributes = array();
        foreach($this->get_attributes() as $key => $value){
            $cleaned_attributes[$key] = $database->escape_value($value);
        }
        return $cleaned_attributes;
    }

    public function create(){
        global $database;
        $attributes = $this->sanitized_attributes();

        // don't forget sql syntax and good habits:
        // INSERT INTO table (key, key) VALUES ('value', 'value')
        // single-quotes aroound all values
        // escape all values to prevent SQL injection

        $sql = "INSERT INTO ". self::$table_name . " (";
        $sql .= join(", ", array_keys($attributes));
        $sql .= ") VALUES ('";
        $sql .= join("', '", array_values($attributes));
        $sql .= "')";
        if($database->query($sql)){
            // we just inserted a record into DB, but we don't hava the id for this,
            // so we get the id using insert_id() and we have everything about this object
            $this->id = $database->insert_id();
            return true;
        }else{
            return false;
        }
    }

    protected function update(){
        global $database;
        $attributes = $this->sanitized_attributes();
        $attribute_pairs = array();
        foreach($attributes as $key => $value){
            $attribute_pairs[] = "{$key}='{$value}'";
        }
        $sql = "UPDATE ". self::$table_name ." SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE id=" . $database->escape_value($this->id);
        $database->query($sql);
        return($database->affected_rows() == 1)? true : false;

    }

    public function delete(){
        global $database;
        $sql = "DELETE FROM " . self::$table_name;
        $sql .= " WHERE id = " . $database->escape_value($this->id);
        $sql .= " LIMIT 1";
        $database->query($sql);
        return($database->affected_rows() == 1)? true : false;

    }
}