<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 06/04/2016
 * Time: 12:27 pm
 */

require_once "database.php";
require_once "publication.php";

class ScholarObject{

    public $id;
    public $title;
    public $url;
    public $year;
    public $num_citations;
    //public $cluster_id;
    public $url_pdf;
    public $url_citations;
    public $excerpt;
    public $member_id;

    protected static $table_name = "scholar";

    public static function find_all(){
        return static::find_by_sql("select * from " . static::$table_name);
    }

    public static function find_by_id($id=0){
        $result_array = static::find_by_sql("select * from ". static::$table_name ." where id ={$id} limit 1");
        // if $result_array empty, then return false, else get the item out of $result_array and return it
        return !empty($result_array)? array_shift($result_array) : false;
    }

    public static function find_by_author_id($id){
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

    public static function instantiate($record){
        // it is good to check $record exists and is an array

        // this is a simple, long form approach to assign values
        $object = new static;
        $attributes = array();

        $reflection = new ReflectionObject($object);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach($properties as $property){
            $attributes[] = $property->getName();
        }
        foreach($record as $attribute => $value){
            if(in_array($attribute, $attributes)){
                $object->$attribute = $record[$attribute];
            }
        }
        return $object;
    }

    // after search obtain results, we need to instantiate temporary object so we can use them around
    public static function initial_object($scholar_object){
        $object = new static;
        $attributes = array();

        $reflection = new ReflectionObject($object);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach($properties as $property){
            $attributes[] = $property->getName();
        }


    }

    public static function search_by_publication_name($pub_name, $member_id){
        global $session;
        $pub_name = trim($pub_name);
        if(mb_strlen($pub_name) == 0){ return false; }
        exec("c:/python27/python.exe c:/wamp/www/scholar-test/scholar.py -p \"{$pub_name}\" -t --csv", $results);
        if(count($results) == 0){
            return 0; //No match
        }
        $new_results = [];
        $count = count($results);
        for($i=0; $i<$count; $i++){
            $q = explode("|", $results[$i]);
            $item = [
                'title'         => $q[0],
                'url'           => $q[1],
                'year'          => $q[2],
                'num_citations' => $q[3],
                'url_pdf'       => $q[6],
                'url_citations' => $q[7],
                'excerpt'       => $q[10],
            ];
            $new_results[] = $item;
        }
        $new_results = static::already_exists_in_publications($new_results, $member_id);
        if(count($new_results) == 0) {
            return 1; // publication already exists.
        }else {
            $inserted_rows = ScholarObject::save_test($new_results, $member_id);
            return $inserted_rows;
        }
    }

    public static function search($author_full_name, $member_id)
    {
        $results = array();
        $start_date = 2003;
        $end_date = date("Y", time());
        for ($i = $start_date; $i <= $end_date ; $i++) { //search from srart_date to end_date
            exec("c:/python27/python.exe c:/wamp/www/scholar-test/scholar.py -a \"{$author_full_name}\" --after={$i} --before={$i} --csv", $results);
        }
            if(count($results) == 0 ) { // if empty, so no result was found
                return 0; // no pub was found
                 }
            $new_results = [];
            $count = count($results); // so we can loop through each one, and if get to $count, we break from loop
            for ($j = 0; $j < $count; ++$j) {
                $q = explode("|", $results[$j]); // each pub in --csv is separated by (|), so we use it as delemeter
                $item = [
                    'title'         => $q[0],
                    'url'           => $q[1],
                    'year'          => $q[2],
                    'num_citations' => $q[3],
                    'url_pdf'       => $q[6],
                    'url_citations' => $q[7],
                    'excerpt'       => $q[10],
                ];
                $new_results[] = $item;
            }
//            echo "<pre>";
//            echo print_r($new_results);
//            echo "</pre>";
        // var_dump($new_results);

//        foreach($results as $result){  // create objects of results instead of dealing with it as array
//            $sanitized_result [] = static::instantiate($result);
//        }

//        $new_results = static::already_exists_in_publications($new_results, $member_id);
        if(count($new_results) == 0) {
            return 1; // no new pub was found
        }else {
            static::save($new_results, $member_id);
            return 2; // results found, go check your list to confirm or ignore
        }


    }

    public static function already_exists_in_publications($scholar_pubs, $member_id){
        $publications = Publication::find_publication_by_author($member_id);
        if(!$publications){ return $scholar_pubs; } // no existed publications, so return all
        foreach($scholar_pubs as $index => $value){
            foreach($publications as $publication){
                if($scholar_pubs[$index]['title'] == $publication->title){
                    unset($scholar_pubs[$index]);
                    break;
                }
            }
        }
        $scholar_pubs = array_values($scholar_pubs);
        return $scholar_pubs;
    }

    public static function already_exists_in_scholar($scholar_pubs, $member_id){
        /* Used in case: member wanna add pub, and the pub is already in scholar table,
        so we need to tell him to check the his list to confirm the pub.*/
        $publications = ScholarObject::find_by_author_id($member_id);
        if(!$publications){ return $scholar_pubs; } // no existed publications, so return all
        for($i=0; $i<count($scholar_pubs); $i++){
            foreach($publications as $publication){
                if($scholar_pubs[$i]['title'] == $publication->title){
                    unset($scholar_pubs[$i]);
                }
            }
        }
        $scholar_pubs = array_values($scholar_pubs);
        return $scholar_pubs;
    }

    public static function already_exists_test(){
        $item = ['title' => 'first t',
        'second' => 'second t',
        'third' => 'third t'];
        $scholar_pubs[] = $item;
        $item = ['dump',
            'title' => 'first dump',
            'second' => 'second dump',
            'third' => 'third dump'];
        $scholar_pubs[] = $item;

        for($i=0; $i<count($scholar_pubs); $i++){
                if (current($scholar_pubs[$i]) == 'dump')
                    unset($scholar_pubs[$i]);

                }

        $scholar_pubs = array_values($scholar_pubs);
        var_dump($scholar_pubs);
    }

    public static function save($results, $member_id){
        global $database;
        foreach($results as $result) {
            $sql = "INSERT INTO " . static::$table_name . " (";
                $sql .= "title, url, url_pdf, url_citations, excerpt, year, num_citations, member_id";
            $sql .= ") VALUES (";
            $sql .= "'{$database->escape_value($result['title'])}', '{$database->escape_value($result['url'])}', ";
            $sql .= "'{$database->escape_value($result['url_pdf'])}', '{$database->escape_value($result['url_citations'])}', ";
            $sql .= "'{$database->escape_value($result['excerpt'])}', ";
            // year might be 'None' or number,
            // so we prepare the statement to prevent error in quotes ('')
            if ($result['year'] == 'None') {
                $sql .= "'{$result['year']}', ";
            }
            else{
                $sql .= "{$result['year']}, ";
            }
            $sql .= "{$result['num_citations']}, {$member_id}";
            $sql .= ")";
            $result_set = $database->query($sql);
        }
    }

    public static function save_test($results, $member_id){
        global $database;
        $inserted_rows = [];
        foreach($results as $result) {
            $sql = "INSERT INTO " . static::$table_name . " (";
            $sql .= "title, url, url_pdf, url_citations, excerpt, year, num_citations, member_id";
            $sql .= ") VALUES (";
            $sql .= "'{$database->escape_value($result['title'])}', '{$database->escape_value($result['url'])}', ";
            $sql .= "'{$database->escape_value($result['url_pdf'])}', '{$database->escape_value($result['url_citations'])}', ";
            $sql .= "'{$database->escape_value($result['excerpt'])}', ";
            // year might be 'None' or number,
            // so we prepare the statement to prevent error in quotes ('')
            if ($result['year'] == 'None') {
                $sql .= "'{$result['year']}', ";
            }
            else{
                $sql .= "{$result['year']}, ";
            }
            $sql .= "{$result['num_citations']}, {$member_id}";
            $sql .= ")";
            if($database->query($sql)){ $inserted_rows [] = $result; }
            return $inserted_rows;
        }
    }

    public function delete(){
        global $database;

        $sql = "DELETE FROM " . ScholarObject::$table_name;
        $sql .= " WHERE id =" . $this->id;
        $database->query($sql);
        return($database->affected_rows() == 1)? true : false;

    }

}

//$item = [
//    'title'         => 'I will live my life as I want, as I imagine',
//    'url'           => 'www.fcit.kau.edu.sa/goodSelfTalk',
//    'year'          => '2017',
//    'num_citations' => 'None',
//    'url_pdf'       => 'www.it is working for me',
//    'url_citations' => 'no pain no gain, I see success coming',
//    'excerpt'       => 'None',
//];
//$results[] = $item;
//$item = [
//    'title'         => 'Hello World',
//    'url'           => 'I am changing, I am stronger, I am tough, hardass',
//    'year'          => 'None',
//    'num_citations' => '15',
//    'url_pdf'       => 'I don\'t care what the peaple think of me',
//    'url_citations' => 'no pain no gain, I see success coming',
//    'excerpt'       => 'None',
//];
//$results[] = $item;
//$title = "Measuring the Effect of CMMI Quality Standard on Agile Scrum Model";
//$result_set = ScholarObject::search_by_publication_name($title, 123147);
////$r = ScholarObject::already_exists_in_publications($results, 123147);
//
//
//
//if($result_set === false) {
//    echo "Enter a title.";
//}elseif($result_set == 0){
//    echo  "No match for the publication title .";
//}elseif($result_set == 1){
//    echo "publication with the name  already exists.";
//}elseif(count($result_set) > 0){
//    echo $result_set;
//}else{
//    echo "Error: propably during form submission.";
//}

