<?php
require_once ("database.php");
require_once ("session.php");


  class DatabaseObject {

    public static function find_all(){
        return static::find_by_sql("select * from " . static::$table_name);
    }

    public static function find_by_id($id=0){
        $result_array = static::find_by_sql("select * from ". static::$table_name ." where id ={$id} limit 1");
        // if $result_array empty, then return false, else get the item out of $result_array and return it
        return !empty($result_array)? array_shift($result_array) : false;
    }

    public static function find_by_sql($sql=""){
        global $database;
        $result_set = $database->query($sql);
        $object_array = array();
        while($row = $database->fetch_array($result_set)){
            $object_array[] = static::instantiate($row);
        }
        return $object_array;
    }

    public static function count_all(){
        global $database;
        $sql= "SELECT COUNT(*) FROM " .static::$table_name;
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

    public static function authenticate($id="", $password=""){
         global $database;
         $id = $database->escape_value($id);
         $password = $database->escape_value($password);

         $sql = "SELECT * FROM " . static::$table_name;
         $sql .= " WHERE id = '{$id}'";
         $sql .= " AND password = '{$password}'";
         $sql .= " LIMIT 1";
         var_dump($sql);
         $result_array = static::find_by_sql($sql);
         return !empty($result_array)? array_shift($result_array) : false;
    }

  private static function instantiate($record){
          // it is good to check $record exists and is an array

          // this is a simple, long form approach to assign values
          $object = new static;
          $attributes = array();
//        $object->id         = $record['id'];
//        $object->password   = $record['password'];
//        $object->first_name = $record['first_name'];
//        $object->last_name  = $record['last_name'];
//        $object->image_file  = $record['image_file'];
//        $object->email  = $record['email'];
//        $object->description  = $record['description'];


          // more dynamic, short form approach to assign values
          // using this class we can get public attributes
          $reflection = new ReflectionObject($object);
          $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
          foreach($properties as $property){
              if($property->getName() == 'errors')
                  continue;
              $attributes[] = $property->getName();
          }
          foreach($record as $attribute => $value){
              if(in_array($attribute, $attributes)){
                  $object->$attribute = $record[$attribute];
              }
          }
          return $object;
      }

    private function has_attribute($attribute){
        $db_fields = static::$db_fields;
          return array_key_exists($attribute, $db_fields);
      }

    protected function get_attributes(){
          $attributes = array();
          foreach(static::$db_fields as $field){
              if(property_exists($this, $field)){
                  $attributes[$field] = $this->$field; // $this->$field; field here is dynamic, don't let that confuse you
              }
          }
          return $attributes;
          /*this will work for our purpose, but if we have a big DB with hundred fields how we can do that?
          we can use SQL statement (SHOW FIELDS FROM users) then we buld our associative array ...*/
      }

    protected function get_sanitized_attributes(){
        global $database;
        $cleaned_attributes = array();
        foreach($this->get_attributes() as $key => $value){
            $cleaned_attributes[$key] = $database->escape_value($value);
        }
        return $cleaned_attributes;
    }

    public function create(){
        global $database;
        $attributes = $this->get_sanitized_attributes();

        $sql = "INSERT INTO ". static::$table_name ." (";
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

    public function update($current_id){
        global $database;
        $attributes = $this->get_sanitized_attributes();
        $attribute_pairs = array();
        foreach($attributes as $key => $value){
            if($key == 'id') { $attribute_pairs[] = "{$key}={$value}"; }
            else { $attribute_pairs[] = "{$key}='{$value}'"; }
        }
        $sql = "UPDATE ". static::$table_name ." SET ";
        $sql .= join(", ", $attribute_pairs);
        $sql .= " WHERE id=" . $database->escape_value($current_id);
        var_dump($sql);
        $database->query($sql);
        return($database->affected_rows() == 1)? true : false;

    }

    public function delete(){
        global $database;
        $sql = "DELETE FROM " . static::$table_name;
        $sql .= " WHERE id = " . $database->escape_value($this->id);
        $sql .= " LIMIT 1";
        $database->query($sql);
        return($database->affected_rows() == 1)? true : false;

    }

    protected function validate_presences($required_fields){
         foreach($required_fields as $field){
             if(empty($this->$field) || $this->$field === ""){
                 $this->errors[] = str_replace('_', ' ', $field) . " can't be blank";
             }
         }
     }

    protected function validate_max_length($array){
         foreach($array as $field => $length){
             if(strlen($this->$field) > $length ){
                 $this->errors[] = str_replace('_', ' ', $field) . " can't exceed {$length}";
             }
         }
     }

    protected function validate_min_length($array){
         foreach($array as $field => $length){
             if(strlen($this->$field) < $length){
                 $this->errors[] = str_replace('_', ' ', $field) . " can't go below {$length}";
             }
         }
     }

    public function validate($presences_check, $max_length_check, $min_length_check){
         $this->validate_presences($presences_check);
         // if some field missing, no further checks
         if(count($this->errors) == 0) {
             $this->validate_max_length($max_length_check);
             $this->validate_min_length($min_length_check);
         }
     }

    public function validate_password($password) {
      global $errors;
      if (!isset($password) || $password === "") {
          $errors [] = 'Password field is empty';  //exit;
      }
      // if(preg_match("/[^\da-zA-Z]/", $password)) $errors [] = "Password has special chars";
      if (!ctype_alnum($password)) $this->errors[] = 'Password has special character';
      if ((strlen($password)) > 30 || (strlen($password)) < 6) $this->errors[] = 'Password must be between 8 - 15';
      if (strcspn($password, '0123456789') == strlen($password)) $this->errors[] = 'Password has NO numbers';
      if (strcspn($password, 'abcdefghijklmnopqrstuvwxyz') == strlen($password)) $this->errors[] = 'Password has NO small letters';
      if (strcspn($password, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ') == strlen($password)) $this->errors[] = 'Password has NO capital letters';
  }

    public function attach_file($file){
          // Perform error checking on the form parameters
          if(!$file || empty($file) || !is_array($file)){
              // error: nothing uploaded or wrong argument usage
              $this->errors[] = "No file was uploaded.";
              return false;
          }elseif($file['error'] != 0){
              // error: report what PHP says went wrong
              $this->errors[] = $this->upload_errors[$file['error']];
              return false;
          }else {
              // Set object attributes to the form parameters.
              $this->old_image = $this->image_file;
              $this->temp_path = $file['tmp_name'];
              $this->image_file = basename($file['name']);
              // don't worry about saving to the database yet.
              return true;
          }
      }

    public function save(){
          // first we check if it is update image or new image
//          if(!empty($this->old_image) && $this->old_image != ""){
//              // update the image
//              if($this->update_image()){
//                  return true;
//              }
//              else{
//                  $this->errors[] = "Your image could not be updated, propably your folder does not exists, or Database error";
//              }
//          }

              // *** Make sure there are no errors

              // Can't save if there are pre-existing errors
              if (!empty($this->errors)) {
                  return false;
              }

              // Can't save without the filename and temp location
              if (empty($this->image_file) || empty($this->temp_path)) {
                  $this->errors[] = "The file location was not available.";
                  return false;
              }
              // Determine the target path
              $target_path = "C:/wamp/www/fcit_erm/public/images/"
                  . $this->id ."/";
              if(!file_exists($target_path)){
                  /*Check if the folder to upload exists, if not that means
                   * you can not upload */
                  $this->errors[] = "The folder to upload does not exists.";
                  return false;
              }
              $target_path .= $this->image_file;
              // Make sure the file is not already exists in  the target location
              if (file_exists($target_path)) {
                  $this->errors[] = "The file {$this->image_file} already exists.";
                  return false;
              }
              // *** attemt to move the file

              if (move_uploaded_file($this->temp_path, $target_path)) {
                  //Success
                  // Save a corresponding entry to the database
                  if ($this->create_image()) {
                      $message = "The file was uploaded successfuly";
                      // we are done with temp_file, the file isn't there anymore
                      unset($this->temp_path);
                      if(!$this->destroy()){
                          global $session;
                          $message .= ", But the phisical file could not be deleted";
                          $session->message($message);
                      }
                      return true;
                  }else{
                      $this->errors[] = "Could not update the database";
                      return false;
                  }
              } else {
                  // Failure
                  $this->errors = "The file upload failed, propably due to incorrect permission
                on the upload folder.";
              }
          }


    public function create_image(){
        global $database;
        $sql = "UPDATE " . static::$table_name ;
        $sql .= " SET image_file = '" . $database->escape_value($this->image_file) . "'";
        $sql .= " WHERE id = " . $database->escape_value($this->id);
          if($database->query($sql)){
              /* we just inserted a record into DB, but we don't hava the id for this,
              // so we get the id using insert_id() and we have everything about this object*/
//              $this->id = $database->insert_id();
              return true;
          }else{
              return false;
          }
      }

    protected function update_image(){
          global $database;
          //$target_file = "C:/wamp/www/fcit_erm/public/images/" . $this->old_image;
              $terget_path = "C:/wamp/www/fcit_erm/public/images/" .
                  $this->id . $this->image_file;
              if(move_uploaded_file($this->temp_path, $terget_path)) {
                  $sql = "UPDATE " . static::$table_name;
                  $sql .= " SET image_file = '" . $database->escape_value($this->image_file) . "'";
                  $sql .= " WHERE id=" . $database->escape_value($this->id);
                  $database->query($sql);
                  if ($database->affected_rows() == 1) {
                      if (!$this->destroy()) {
                          /*check $this->errors if any problem, */
                          $this->errors[] = "the physical file could not be removed";
                      }
                      return true;
                  } else {
                      $this->errors[] = "Database could not be updated.";
                      return false;
                  }
              }else{
                  $this->errors = "The file upload failed, propably due wrong upload folder.";
                  return false;
              }
      }

    public static function filterSearchKeys($query){
          $query = trim(preg_replace("/(\s+)+/", " ", $query));
          $words = array();
          // expand this list with your words.
          $list = array("in","it","a","the","of","or","I","you","he","me","us","they","she","to","but","that","this","those","then");
          $c = 0;
          foreach(explode(" ", $query) as $key){
              if (in_array($key, $list)){
                  continue;
              }
              $words[] = $key;
              if ($c >= 15){
                  break;
              }
              $c++;
          }
          return $words;
      }

    // limit words number of characters

    public static function limitChars($query, $limit = 200){
          return substr($query, 0,$limit);
      }

    public static function search($query){

    }

  }
?>