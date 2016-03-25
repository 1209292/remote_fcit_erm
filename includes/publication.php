<?php



class Publication {

    public $id;
    public $title;
    public $website;
    public $url;
    public $hits;
    public $keywords;
    public $date;

    protected static $table_name = "publications";
    protected static $db_fields = array('id', 'title', 'website', 'url', 'hits',
        'keywords', 'date');

    public static function find_all(){
        return static::find_by_sql("select * from " . static::$table_name);
    }

    public static function find_by_id($id=0){
        $result_array = static::find_by_sql("select * from ". static::$table_name ." where id ={$id} limit 1");
        // if $result_array empty, then return false, else get the item out of $result_array and return it
        return !empty($result_array)? array_shift($result_array) : false;
    }

    /**
     * @param int $id
     * @return bool|mixed
     */
    public static function find_publication_by_member_id($id=0){
        $result_array = static::find_by_sql("select * from ". static::$table_name ." where member_id ={$id}");
        // if $result_array empty, then return false, else get the item out of $result_array and return it
        return !empty($result_array)? $result_array : false;
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

    public function save(){
    // first we check if it is update image or new image
    if(!empty($this->old_image) && $this->old_image != "" && !is_null($this->image_file)){
        // update the image
        if($this->update_image()){
            return true;
        }
        else{
            $this->errors[] = "Your image could not be uploaded";
        }
    } else {
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
        $terget_path = "C:/wamp/www/fcit_erm/public/images/" . $this->image_file;
        // Make sure the file is not already exists in  the target location
        if (file_exists($terget_path)) {
            $this->errors[] = "The file {$this->image_file} already exists.";
            return false;
        }
        // *** attemt to move the file
        if (move_uploaded_file($this->temp_path, $terget_path)) {
            //Success
            // Save a corresponding entry to the database
            if ($this->create_image()) {
                // we are done with temp_file, the file isn't there anymore
                unset($this->temp_path);
                return true;
            }
        } else {
            // Failure
            $this->errors = "The file upload failed, propably due to incorrect permission
                on the upload folder.";
        }
    }
}


}
?>