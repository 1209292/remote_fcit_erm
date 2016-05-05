<?php

/**
 * Created by PhpStorm.
 * User: windows
 * Date: 14/03/2016
 * Time: 04:15 am
 */

require_once ("database.php");
require_once ("database_object.php");
class Upload extends DatabaseObject{

    protected static $table_name = "uploads";
    protected static $db_fields = array('id', 'filename', 'type',
        'size', 'caption', 'member_id');
    public $id;
    public $filename;
    public $type;
    public $size;
    public $caption;
    public $member_id;
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

    public static function uploads_find_all($per_page, $offset){
        return static::find_by_sql("select * from " . static::$table_name. " LIMIT {$per_page} OFFSET {$offset}");
    }

    /**** used in pagination ****/
    public static function count_all_by_author($id){
        global $database;
        $sql= "SELECT COUNT(*) FROM " .static::$table_name . " where member_id = $id";
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

    // Pass in $_FILES['uoloaded_file'] as an argument
    public function attach_file_upload($file, $member_id, $caption){
        // if file already exists, ask either to replace, keep both or cancle.
        // if(check_existance($file, $member_id)){}

        // Perform error checking on the form parameters
        if(!$file || empty($file) || !is_array($file)){
            // error: nothing uploaded or wrong argument usage
            $this->errors[] = "No file was uploaded.";
            return false;
        }elseif($file['error'] != 0){
            // error: report what PHP says went wrong
            $this->errors[] = $this->upload_errors[$file['error']];
            return false;
        }else{
            // Set object attributes to the form parameters.
            $this->temp_path = $file['tmp_name'];
            $this->filename = basename($file['name']);
            $this->type = $file['type'];
            $this->size = $file['size'];
            $this->caption = $caption;
            $this->member_id = $member_id;
            // don't worry about saving to the database yet.
            return true;
        }
    }

    public function save(){
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
            $target_path = "C:/wamp/www/fcit_erm/public/uploads/" .
                $this->member_id ."/";
            if(!file_exists($target_path)){
                /* check if folder of the member exists or not, we can not upload
                // if the folder does not exists*/
                $this->errors[] = "The folder to upload does not exists.";
                return false;
            }
            $target_path .= $this->filename;
            // Make sure the file is not already exists in  the target location
            if(file_exists($target_path)){
                $this->errors[] = "The file {$this->filename} already exists.";
                return false;
            }
            // *** attemt to move the file

            if(move_uploaded_file($this->temp_path, $target_path)){
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


    /* this method will do the second step of deleting which is deleting
        the physical file from the machine */
    public function destroy(){
            $target_path = "C:/wamp/www/fcit_erm/public/uploads/" .
                $this->member_id . "/" . $this->filename;
            return unlink($target_path) ? true : false;
}

    public static function destroy_assets($id){

        /*For now we going to delete uploads/$member_id folder only, until we see what
        we do with images/$member_id folder*/
        $uploads_path = $_SERVER['DOCUMENT_ROOT'] . "fcit_erm/public/uploads/" .
            $id ."/";
        $result = static::deleteDir($uploads_path);
        return $result;
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

// (not completed): checks if upload already exists, if so ask user to delete, replace or keep both
    public function check_existance($file, $member_id){
        global $database;
        $sql = "SELECT * from " . Upload::$table_name;
        $sql .= "WHERE member_id=" . $database->escape_value($member_id);
        $upload_set = $database->query($sql);
        // not solved: what if the query was not performed, or no result_set found

        // first: check type, if no match --> second: check filename
        foreach($upload_set as $upload) {
            $upload = static::instantiate($upload);
            if($file['type'] == $upload->type && $file['name'] == $upload->filename){
                // Do something
            }
        }
    }

    public static function find_uploads_by_member_id($id=0, $per_page=0, $offset=0){
        global $database;
        if($per_page==0 && $offset==0){
            $result_array = static::find_by_sql("SELECT * FROM " . static::$table_name . " WHERE member_id="
                ."{$database->escape_value($id)}");
        }else {
            $result_array = static::find_by_sql("SELECT * FROM " . static::$table_name . " WHERE member_id="
            ."{$database->escape_value($id)} LIMIT {$per_page} OFFSET {$offset}");
        }
        // if $result_array empty, then return false, else get the item out of $result_array and return it
        return !empty($result_array)? $result_array : false;
    }

//    public function update($id){
//        global $database;
//        $attributes = $this->get_sanitized_attributes();
//        $attribute_pairs = array();
//        foreach($attributes as $key => $value){
//            $attribute_pairs[] = "{$key}='{$value}'";
//        }
//        $sql = "UPDATE ". self::$table_name ." SET ";
//        $sql .= join(", ", $attribute_pairs);
//        $sql .= " WHERE id=" . $database->escape_value($this->id);
//        $database->query($sql);
//        return($database->affected_rows() == 1)? true : false;
//
//    }

    public static function search($query){

        parent::search($query); // TODO: Change the autogenerated stub
        global $database;
        $query = trim($query);
        if (mb_strlen($query)===0){
            // no need for empty search right?
            return false;
        }

        $query = static::limitChars($query);

        // Weighing scores
        $scoreFullTitle = 6;
        $scoreTitleKeyword = 5;
        $scoreFullSummary = 5;
        $scoreSummaryKeyword = 4;


        $keywords = static::filterSearchKeys($query);
        $escQuery = $database->escape_value($query);
        $filenameSQL = array();
        $captionSQL = array();


        /** Matching full occurences **/
        if (count($keywords) > 1){
            $filenameSQL[] = "if (filename LIKE '%".$escQuery."%',{$scoreFullTitle},0)";
            $captionSQL[] = "if (caption LIKE '%".$escQuery."%',{$scoreFullSummary},0)";
        }
        /** Matching Keywords **/
        foreach($keywords as $key){
            $filenameSQL[] = "if (filename LIKE '%".$database->escape_value($key)."%',{$scoreTitleKeyword},0)";
            $captionSQL[] = "if (caption LIKE '%".$database->escape_value($key)."%',{$scoreSummaryKeyword},0)";
        }

        $sql = "SELECT *,
            (
                (-- Title score
                ".implode(" + ", $filenameSQL)."
                )+
                (-- Summary
                ".implode(" + ", $captionSQL)."
                )
            ) as relevance
            FROM uploads
            HAVING relevance > 0
            order by relevance Desc";
        $results = static::find_by_sql($sql);
        if (count($results) <= 0){
            return false;
        }
        return $results;
    }
}