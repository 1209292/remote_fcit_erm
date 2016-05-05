<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 06/04/2016
 * Time: 12:27 pm
 */

require_once "database.php";
require_once "publication.php";

class ScholarObject
{

    public $id;
    public $title;
    public $url;
    public $year;
    public $num_citations;
    public $url_pdf;
    public $url_citations;
    public $excerpt;
    public $member_id;

    protected static $table_name = "scholar";

    public static function find_all()
    {
        return static::find_by_sql("select * from " . static::$table_name);
    }

    public static function find_by_id($id = 0)
    {
        $result_array = static::find_by_sql("select * from " . static::$table_name . " where id ={$id} limit 1");
        // if $result_array empty, then return false, else get the item out of $result_array and return it
        return !empty($result_array) ? array_shift($result_array) : false;
    }

    public static function find_by_author_id($id, $per_page=0, $offset=0)
    {
        if($per_page==0 && $offset==0){
            $result_array = static::find_by_sql("select * from " . static::$table_name . " where member_id ={$id}");
        }else {
            $result_array = static::find_by_sql("select * from " . static::$table_name . " where member_id ={$id}"
                . " LIMIT {$per_page} OFFSET {$offset}");
        }
        // if $result_array empty, then return false, else get the item out of $result_array and return it
        return !empty($result_array) ? $result_array : false;
    }

    public static function find_by_sql($sql = "")
    {
        global $database;
        $result_set = $database->query($sql);
        $object_array = array();
        while ($row = $database->fetch_array($result_set)) {
            $object_array[] = static::instantiate($row);
        }
        return $object_array;
    }

    public static function instantiate($record)
    {
        // it is good to check $record exists and is an array

        // this is a simple, long form approach to assign values
        $object = new static;
        $attributes = array();

        $reflection = new ReflectionObject($object);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            $attributes[] = $property->getName();
        }
        foreach ($record as $attribute => $value) {
            if (in_array($attribute, $attributes)) {
                $object->$attribute = $record[$attribute];
            }
        }
        return $object;
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

    // after search obtain results, we need to instantiate temporary object so we can use them around
    public static function initial_object($scholar_object)
    {
        $object = new static;
        $attributes = array();

        $reflection = new ReflectionObject($object);
        $properties = $reflection->getProperties(ReflectionProperty::IS_PUBLIC);
        foreach ($properties as $property) {
            $attributes[] = $property->getName();
        }


    }

    public static function search_by_publication_name($pub_name)
    {
        global $session;
        $pub_name = trim($pub_name);
        if (mb_strlen($pub_name) == 0) {
            return false;
        }
        exec("c:/python34/python.exe c:/wamp/www/scholar-test/scholar.py -p \"{$pub_name}\" -t --csv", $results);
        if (count($results) == 0) {
            return 0; //No match
        }
        $new_results = [];
        $count = count($results);
        for ($i = 0; $i < $count; $i++) {
            $q = explode("|", $results[$i]);
            $item = [
                'title' => $q[0],
                'url' => $q[1],
                'year' => $q[2],
                'num_citations' => $q[3],
                'url_pdf' => $q[6],
                'url_citations' => $q[7],
                'excerpt' => $q[10],
            ];
            $new_results[] = $item;
        }

//        $sanitized_arr = ScholarObject::already_exists_in_publications($new_results, $member_id);

        return (count($new_results) > 0) ? $new_results : false;
    }

    public static function my_search($author_full_name, $publ_name="")
    {
        $result = array();
        if($publ_name == "") {
            exec("c:/python34/python.exe " . __DIR__ . "/my_api.py \"" . $author_full_name . "\"", $result);
        }else{
            exec("c:/python34/python.exe " . __DIR__ . "/my_api_by_name.py \"" . $author_full_name . "\" \"".$publ_name."\"", $result);
        }
        $new_results = [];
        $count = count($result); // so we can loop through each one, and if get to $count, we break from loop
        for ($j = 0; $j < $count; ++$j) {
            $q = explode("|", $result[$j]); // each pub in --csv is separated by (|), so we use it as delemeter
            if (filter_var($q[1], FILTER_VALIDATE_INT)) {
                $item = [
                    'title' => $q[0],
                    'num_citations' => $q[1],
                    'year' => $q[2],
                    'url' => $q[4],
                    'url_pdf' => 'None',
                    'url_citations' => $q[3],
                    'excerpt' => 'None',
                ];
            } else {
                $item = [
                    'title' => $q[0],
                    'num_citations' => 0,
                    'year' => $q[2],
                    'url' => $q[4],
                    'url_pdf' => 'None',
                    'url_citations' => 'None',
                    'excerpt' => 'None',
                ];
            }
            $new_results[] = $item;
        }
        return (count($new_results) > 0) ? $new_results : false;
    }

    // api search
    public static function search($author_full_name)
    {
        $results = array();
        $start_date = 2003;
        $end_date = date("Y", time());
        for ($i = $start_date; $i <= $end_date; $i++) { //search from srart_date to end_date
            exec("c:/python34/python.exe c:/wamp/www/scholar-test/scholar.py -a \"{$author_full_name}\" --after={$i} --before={$i} --csv", $results);
            sleep(rand(1, 4)); // used to not be abanded by google
        }
        $new_results = [];
        $count = count($results); // so we can loop through each one, and if get to $count, we break from loop
        for ($j = 0; $j < $count; ++$j) {
            $q = explode("|", $results[$j]); // each pub in --csv is separated by (|), so we use it as delemeter
            $item = [
                'title' => $q[0],
                'url' => $q[1],
                'year' => $q[2],
                'num_citations' => $q[3],
                'url_pdf' => $q[6],
                'url_citations' => $q[7],
                'excerpt' => $q[10],
            ];
            $new_results[] = $item;
        }
        return (count($new_results) > 0) ? $new_results : false;
    }

    // in case we hava an object and we wanna compare with publications (e.g. wait_list.php in case of 'add' or 'add_all')
    public static function object_already_exists_in_publications($scholar_pubs, $member_id){
        $publications = Publication::find_publication_by_author($member_id);
        if(!$publications){ return $scholar_pubs; } // no existed publications, so return all
        foreach($scholar_pubs as $index => $value){
            foreach($publications as $publication){
                if(strtolower($scholar_pubs[$index]->title) == strtolower($publication->title)){
                    unset($scholar_pubs[$index]);
                    break;
                }
            }
        }
        $scholar_pubs = array_values($scholar_pubs);
        return (count($scholar_pubs) > 0) ? $scholar_pubs : false ;
    }

    // in case we hava an array and we wanna compare with publications (e.g. add_public.php in case of 'add_manually' or 'add_automatically'
    public static function already_exists_in_publications($scholar_pubs, $member_id){
        $publications = Publication::find_publication_by_author($member_id, 0, 0);
        if(!$publications){ return $scholar_pubs; } // no existed publications, so return all
        foreach($scholar_pubs as $index => $value){
            foreach($publications as $publication){
                if(strtolower($scholar_pubs[$index]['title']) == strtolower($publication->title)){
                    unset($scholar_pubs[$index]);
                    break;
                }
            }
        }
        $scholar_pubs = array_values($scholar_pubs);
        return (count($scholar_pubs) > 0) ? $scholar_pubs : false ;
    }

    public static function already_exists_in_scholar($scholar_pubs, $member_id){
        $publications = ScholarObject::find_by_author_id($member_id);
        if(!$publications){ return $scholar_pubs; } // no existed publications, so return all
        foreach($scholar_pubs as $index => $value){
            foreach($publications as $publication){
                if(strtolower($scholar_pubs[$index]['title']) == strtolower($publication->title)){
                    unset($scholar_pubs[$index]);
                    break;
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

    public static function save($item, $member_id){
        global $database;
            $sql = "INSERT INTO " . static::$table_name . " (";
            $sql .= "title, url, url_pdf, url_citations, excerpt, year, num_citations, member_id";
            $sql .= ") VALUES (";
            $sql .= "'{$database->escape_value($item['title'])}', '{$database->escape_value($item['url'])}', ";
            $sql .= "'{$database->escape_value($item['url_pdf'])}', '{$database->escape_value($item['url_citations'])}', ";
            $sql .= "'{$database->escape_value($item['excerpt'])}', ";
            // year might be 'None' or number,
            // so we prepare the statement to prevent error in quotes ('')
            if ($item['year'] == 'None') {
                $sql .= "'{$item['year']}', ";
            }
            else{
                $sql .= "{$item['year']}, ";
            }
            $sql .= "{$item['num_citations']}, {$member_id}";
            $sql .= ")";
            $database->query($sql);
            return ($database->affected_rows() == 1) ? true : false ;
    }

    public function delete(){
        global $database;

        $sql = "DELETE FROM " . ScholarObject::$table_name;
        $sql .= " WHERE id =" . $this->id;
        $database->query($sql);
        return($database->affected_rows() == 1)? true : false;

    }

//    public function save_object($member_id)
//    {
//        global $database;
//        $sql = "INSERT INTO " . static::$table_name . " (";
//        $sql .= "title, url, url_pdf, url_citations, excerpt, year, num_citations, member_id";
//        $sql .= ") VALUES (";
//        $sql .= "'{$database->escape_value($this->title)}', '{$database->escape_value($this->url)}', ";
//        $sql .= "'{$database->escape_value($this->url_pdf)}', '{$database->escape_value($this->url_citations)}', ";
//        $sql .= "'{$database->escape_value($this->excerpt)}', ";
//        // year might be 'None' or number,
//        // so we prepare the statement to prevent error in quotes ('')
//        if ($this->year == 'None') {
//            $sql .= "'{$this->year}', ";
//        } else {
//            $sql .= "{$this->year}, ";
//        }
//        $sql .= "{$this->num_citations}, {$member_id}";
//        $sql .= ")";
//        $database->query($sql);
//        return ($database->affected_rows() == 1)? true : false;
//    }
}
/*
//$item = [
//    'title'         => 'Novel sequence variants in the TMC1 gene in Pakistani families with autosomal recessive hearing impairment',
//    'url'           => 'www.fcit.kau.edu.sa/goodSelfTalk',
//    'year'          => '2017',
//    'num_citations' => 0,
//    'url_pdf'       => 'www.it is working for me',
//    'url_citations' => 'no pain no gain, I see success coming',
//    'excerpt'       => 'None',
//];
//$results[] = $item;
//$item = [
//    'title'         => 'Hello World',
//    'url'           => 'I am changing, I am stronger, I am tough, hardass',
//    'year'          => 'None',
//    'num_citations' => 0,
//    'url_pdf'       => 'I don\'t care what the peaple think of me',
//    'url_citations' => 'no pain no gain, I see success coming',
//    'excerpt'       => 'None',
//];
//$results[] = $item;
//$count = count($results);
//for($i=0; $i<$count; $i++) {
//    $re = ScholarObject::save(array_shift($results), 123147);
//    echo $re === true ? 'true' : 'false' ;
//}
//foreach($results as $index => $val){
//    if($results [$index]['title'] == 'Hello World')
//        $results [$index]['title'] = "Welcome Everyone";
//}
//var_dump($results);
//$title = "A comprehensive study of commonly practiced heavy and light weight software methodologies";
//$title_2 = "A Comprehensive Study of Commonly Practiced Heavy and Light Weight Software Methodologies";
//if($title_1 == $title_2){
//    echo "true";
//} else{
//    echo "false";
//}
//var_dump($results);
//$result = ScholarObject::search_no_check("Asif Irshad Khan", "A comprehensive study of commonly practiced heavy and light weight software methodologies");
//$result = ScholarObject::search_by_publication_name("TextOntoEx: Automatic ontology construction from natural English text");
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
*/