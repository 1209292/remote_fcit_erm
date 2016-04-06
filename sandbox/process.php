<?php
$database = mysqli_connect("localhost", "root", "j", "test");
 function find_all(){
    return find_by_sql("select * from dump");
}

 function find_by_id($id=0){
    $result_array = find_by_sql("select * from dump where id ={$id} limit 1");
    // if $result_array empty, then return false, else get the item out of $result_array and return it
    return !empty($result_array)? array_shift($result_array) : false;
}

 function find_by_sql($sql=""){
    global $database;
    $result_set = $database->query($sql);
//    $object_array = array();
//    while($row = $database->fetch_array($result_set)){
//        $object_array[] = instantiate($row);
//    }
    return $result_set;
}

 function count_all(){
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

 function authenticate($id="", $password=""){
    global $database;
    $id = $database->escape_value($id);
    $password = $database->escape_value($password);

    $sql = "SELECT * FROM dump";
    $sql .= " WHERE id = '{$id}'";
    $sql .= " AND password = '{$password}'";
    $sql .= " LIMIT 1";
    $result_array = find_by_sql($sql);
    return !empty($result_array)? array_shift($result_array) : false;
}

 function instantiate($record){
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
$search = null;
echo mysqli_affected_rows($database);
if($search){
    echo "Success";
}else{
    echo "Failed";
}


?>