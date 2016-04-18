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

    public static function search_by_publication_name($pub_name, $member_full_name, $member_id){
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
        return (count($new_results) > 0) ? $new_results : false ;
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
        return (count($new_results) > 0) ? $new_results : false ;
    }

    public static function already_exists_in_publications($scholar_pubs, $member_id){
        $publications = Publication::find_publication_by_author($member_id);
        if(!$publications){ return $scholar_pubs; } // no existed publications, so return all
        foreach($scholar_pubs as $index => $value){
            foreach($publications as $publication){
                $str = strtolower($scholar_pubs[$index]['title']);
                if($str == strtolower($publication->title)){
                    unset($scholar_pubs[$index]);
                    break;
                }
            }
        }
        $scholar_pubs = array_values($scholar_pubs);
        return (count($scholar_pubs) > 0) ? $scholar_pubs : false ;
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
        }
        return count($inserted_rows);
    }

    public function delete(){
        global $database;

        $sql = "DELETE FROM " . ScholarObject::$table_name;
        $sql .= " WHERE id =" . $this->id;
        $database->query($sql);
        return($database->affected_rows() == 1)? true : false;

    }

}

$item = [
    'title'         => 'Novel sequence variants in the TMC1 gene in Pakistani families with autosomal recessive hearing impairment',
    'url'           => 'www.fcit.kau.edu.sa/goodSelfTalk',
    'year'          => '2017',
    'num_citations' => 'None',
    'url_pdf'       => 'www.it is working for me',
    'url_citations' => 'no pain no gain, I see success coming',
    'excerpt'       => 'None',
];
$results[] = $item;
$item = [
    'title'         => 'Hello World',
    'url'           => 'I am changing, I am stronger, I am tough, hardass',
    'year'          => 'None',
    'num_citations' => '15',
    'url_pdf'       => 'I don\'t care what the peaple think of me',
    'url_citations' => 'no pain no gain, I see success coming',
    'excerpt'       => 'None',
];
$results[] = $item;
foreach($results as $index => $val){
    if($results [$index]['title'] == 'Hello World')
        $results [$index]['title'] = "Welcome Everyone";
}
var_dump($results);
//$title = "A comprehensive study of commonly practiced heavy and light weight software methodologies";
//$title_2 = "A Comprehensive Study of Commonly Practiced Heavy and Light Weight Software Methodologies";
//if($title_1 == $title_2){
//    echo "true";
//} else{
//    echo "false";
//}
//var_dump($results);
//$result = ScholarObject::search_no_check("Asif Irshad Khan", "A comprehensive study of commonly practiced heavy and light weight software methodologies");
//$result = ScholarObject::search_by_publication_name("A comprehensive study of commonly practiced heavy and light weight software methodologies", "Asif Irshad Khan", 111);
//var_dump($result);
//$result2 = ScholarObject::already_exists_in_publications($result, 123147);
//var_dump($result2);
//$r = ScholarObject::already_exists_in_publications($result, 123456);
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
//    echo "OK.";
//}else{
//    echo "Error: propably during form submission.";
//}
