<?php


require_once "database.php";
require_once "member.php";

class Publication {

    public $id;
    public $title;
    public $url;
    public $url_pdf;
    public $url_citations;
    public $num_citations;
    public $excerpt;
    public $keywords;
    public $year;
    public $hits;
    public $member_id;

    protected static $table_name = "publications";
    protected static $db_fields = array('id', 'title', 'website', 'url', 'hits',
        'keywords', 'year');

    public static function find_all($per_page=0, $offset=0){
        if($per_page==0 && $offset==0 ){
            return static::find_by_sql("select * from " . static::$table_name);
        }else {
            return static::find_by_sql("select * from " . static::$table_name
                . " ORDER BY hits DESC LIMIT {$per_page} OFFSET {$offset}");
        }
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
    public static function find_publication_by_author($id=0, $per_page=0, $offset=0){
        if($per_page == 0 && $offset == 0){
            $result_array = static::find_by_sql("SELECT * FROM " . static::$table_name . " WHERE member_id ={$id}");
        }else {
            $result_array = static::find_by_sql("SELECT * FROM " . static::$table_name . " WHERE member_id ={$id} LIMIT {$per_page} OFFSET {$offset}");
        }
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

    private static function instantiate($record){
        // it is good to check $record exists and is an array

        // this is a simple, long form approach to assign values
        $object = new static;
        $attributes = array();

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

    public static function save($scholar_pub, $member_id) { // recieves from Scholar_object
        global $database;
        $inserted_rows = [];
        foreach ($scholar_pub as $pub) {
            $keywords = Publication::make_search_keys($pub->title); // to help us on local search
            $sql = "INSERT INTO " . static::$table_name . "(";
            $sql .= "title, url, url_pdf, url_citations, excerpt, year, num_citations, member_id";
            if($keywords){ $sql .= ", keywords";} // there is some keywords
            $sql .= ") VALUES (";
            $sql .= "'{$database->escape_value($pub->title)}', '{$database->escape_value($pub->url)}', ";
            $sql .= "'{$database->escape_value($pub->url_pdf)}', '{$database->escape_value($pub->url_citations)}', ";
            $sql .= "'{$database->escape_value($pub->excerpt)}', ";
            if ($pub->year == 'None') {
                $sql .= "'{$pub->year}', ";
            }
            else{
                $sql .= "{$pub->year}, ";
            }
            $sql .= "{$pub->num_citations}, {$member_id}";
            if($keywords){ $sql .= ", '{$keywords}'"; }
            $sql .= ")";
            if($database->query($sql)){ $inserted_rows [] = $pub; }
        }
        return (count($inserted_rows) > 0) ? $inserted_rows : false;
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

    public static function make_search_keys($title){
        $title = trim($title);
        if(mb_strlen($title) == 0) { return false; }
        $title = trim(preg_replace("/(\s+)+/", " ", $title));
        $words = "";
        // expand this list with your words.
        $list = array("in","it","a","the","of","or","I","you","he","me","us","they","she","to","but","that","this","those","then");
        $c = 0;
        foreach(explode(" ", $title) as $key){
            if (in_array($key, $list)){
                continue;
            }
            $words .= $key . " ";
            if ($c >= 15){
                break;
            }
            $c++;
        }
        return preg_replace('/[^A-Za-z0-9\-]/', ' ', $words); // remove special chars and replace with space
    }

    // limit words number of characters
    public static function limitChars($query, $limit = 200){
        return substr($query, 0,$limit);
    }

    public static function search($query){

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
        $keys = array();
        $last_name = array();

        /** Matching full occurences **/
        if (count($keywords) > 1){
            $keys[] = "if (keywords LIKE '%".$escQuery."%',{$scoreFullTitle},0)";
//            $last_name[] = "if (last_name LIKE '%".$escQuery."%',{$scoreFullSummary},0)";
        }
        /** Matching Keywords **/
        foreach($keywords as $key){
            $keys[] = "if (keywords LIKE '%".$database->escape_value($key)."%',{$scoreTitleKeyword},0)";
//            $last_name[] = "if (last_name LIKE '%".$database->escape_value($key)."%',{$scoreSummaryKeyword},0)";
        }

        $sql = "SELECT *,
            (
                (-- Title score
                ".implode(" + ", $keys)."
                )
            ) as relevance
            FROM publications
            HAVING relevance > 0
            order by relevance Desc";
        $results = static::find_by_sql($sql);
        if (count($results) <= 0){
            return false;
        }
        return $results;
    }

    public function hits($dump="")
    {
        if ($dump == "") { // this is get hits
            return $this->hits;
        }else{  // this is increments hits
            global $database;
            $sql = "UPDATE " . static::$table_name;
            $sql .= " SET hits=" . $this->hits++;
            $sql .= " WHERE id=" . $this->id;
            $database->query($sql);
            return ($database->affected_rows() == 1) ? true : false;
    }
}

    /*** if $id provided: get top ten cited publc of $id, else get top ten cited publc ***/
    public static function most_cited_publc_list($author_id=""){
        global $database;
        $returned_results = [];
        if($author_id == ""){ // get the top ten cited publications
            $sql = "SELECT *  FROM " . static::$table_name . " ORDER BY num_citations DESC LIMIT 10 OFFSET 0";
            $result_set = $database->query($sql);
            if(count($result_set) > 0){
                foreach($result_set as $result){
                    $returned_results[] = static::instantiate($result);
                }
                return $returned_results;
            }else{
                return false;
            }
        }else{ // $author givin, so get most cited publications of $author
            $sql = "SELECT *  FROM " . static::$table_name;
            $sql .= "  WHERE member_id= " .$author_id. " ORDER BY num_citations DESC LIMIT 10 OFFSET 0";
            $result_set = $database->query($sql);
            if(count($result_set) > 0){
                foreach($result_set as $result){
                    $returned_results[] = static::instantiate($result);
                }
                return $returned_results;
            }else{
                return false;
            }
        }
    }

    public static function most_visited_publc_list($author_id=""){
        global $database;
        $returned_results = [];
        if($author_id == ""){ // get the top ten cited publications
            $sql = "SELECT *  FROM " . static::$table_name . " ORDER BY hits DESC LIMIT 10 OFFSET 0";
            $result_set = $database->query($sql);
            if(count($result_set) > 0){
                foreach($result_set as $result){
                    $returned_results[] = static::instantiate($result);
                }
                return $returned_results;
            }else{
                return false;
            }
        }else{ // $author givin, so get most cited publications of $author
            $sql = "SELECT *  FROM " . static::$table_name;
            $sql .= "  WHERE member_id= " .$author_id. " ORDER BY hits DESC LIMIT 10 OFFSET 0";
            $result_set = $database->query($sql);
            if(count($result_set) > 0){
                foreach($result_set as $result){
                    $returned_results[] = static::instantiate($result);
                }
                return $returned_results;
            }else{
                return false;
            }
        }
    }

    /*** return assoc array each index represents the publications of that index (index is a year) ***/
    public static function author_publc_by_year($member_id){
        global $database;
        $returned_arr = [];
        $publications = static::find_publication_by_author($member_id, 0, 0);
        if(!$publications){return false;}
        $start_year = 2000;
        $end_year = date("Y" ,time());
        for($i=$start_year; $i<=$end_year; $i++) {
            foreach ($publications as $key => $value) {
                if($publications[$key]->year == $i){
                    // ["'$i'"] will save $i as string, not a number, so we don't waste space
                    $returned_arr[$i][] = $publications[$key];
                }
                if($publications[$key]->year == 'None'){
                    // ["'$i'"] will save $i as string, not a number, so we don't waste space
                    $returned_arr[0][] = $publications[$key];
                }
            }
        }
        return $returned_arr;
    }

    // $year not provided: number of oublc in each year, else return publc only in that year
    public static function publc_by_year($year = ""){
        global $database;
        $returned_arr = [];
        if($year == "") {
            $publications = static::find_all(0, 0);
            if (!$publications) {
                return false;
            }
            $start_year = 2000;
            $end_year = date("Y", time());
            $count = 0;
            for ($i = $start_year; $i <= $end_year; $i++) {
                foreach ($publications as $key => $value) {
                    if ($publications[$key]->year == $i) {
                        $count++;
                    }
                }
                if($count > 0){ $returned_arr[$i] = $count; }
            }
            return count($returned_arr) > 0 ? $returned_arr : false ;
        }else{
            $publications = static::find_by_sql("SELECT * FROM publications WHERE year=" . $year);
            return count($publications) > 0 ? $publications : false ;
        }
    }

    // top cited author
    public static function ballondor_author_in_citation(){
        global $database;
        $authors = Member::find_all();
        $winner = "";
        $max = 0;
        for($i=0; $i<count($authors); $i++) {
            $cite_count = Publication::get_count_citations("member", $authors[$i]->id);
            if($cite_count > $max){
                $winner = $authors[$i];
                $max = $cite_count;
            }
        }
        return $winner != "" ? $winner : false;
    }

    // top publishing author
    public static function ballondor_author_in_publc(){
        $authors = Member::find_all();
        $winner = "";
        $max = 0;
        for($i=0; $i<count($authors); $i++) {
            $publc_count = Publication::get_count_publc($authors[$i]->id);
            if($publc_count > $max){
                $winner = $authors[$i];
                $max = $publc_count;
            }
        }
        return $winner != "" ? $winner : false;
    }

    // top cited publc
    public static  function ballondor_publc(){
        global $database;
        $publications = Publication::find_all(0, 0);
        $winner = "";
        $max = 0;
        for($i=0; $i<count($publications); $i++) {
            $cite_count = Publication::get_count_citations("publication", $publications[$i]->id);
            if($cite_count > $max){
                $winner = $publications[$i];
                $max = $cite_count;
            }
        }
        return $winner != "" ? $winner : false;
    }

    /* if $id not provided: number of citations of all members,
       if $id is there and $id_type='member', get number of cit. of an author
       if $id is there and $id_type='publication', get number of cit. of a publication
    */
    public static function get_count_citations($id_type = "", $id = ""){
        global $database;
        if($id_type == "" && $id == ""){
            $sql = "SELECT SUM(num_citations) FROM publications";
            $result_set = $database->query($sql);
            $result = $database->fetch_array($result_set);
            // $result[0] will have null if no citations existed
            return ($result[0] != null && count($result) > 0) ? $result[0] : false;
        }elseif($id_type == "member"){
            $sql = "SELECT SUM(num_citations) FROM publications WHERE member_id=" . $id;
            $result_set = $database->query($sql);
            $result = $database->fetch_array($result_set);
            // $result[0] will have null if no citations existed for $id
            return ($result[0] != null && count($result) > 0) ? $result[0] : false;
        }else{
            $sql = "SELECT num_citations FROM publications WHERE id=" . $id;
            $result_set = $database->query($sql);
            $result = $database->fetch_array($result_set);
            // $result[0] will have null if no citations existed for $id
            return ($result[0] != null && count($result) > 0) ? $result[0] : false;
        }
    }

    // id not provided: count of all members publications, else get pub. by author id
    public static function get_count_publc($id = ""){
        global $database;
        // count return ZERO pub. id not existed
        if($id == "") {
            $sql = "SELECT COUNT(*) FROM publications";
            $result = $database->query($sql);
            $num = $database->fetch_array($result);
            return count($num) > 0 ? $num[0] : false;
        }else{
            // count return ZERO if id not existed
            $sql = "SELECT COUNT(*) FROM publications WHERE member_id=" . $id;
            $result = $database->query($sql);
            $num = $database->fetch_array($result);
            return count($num) > 0 ? $num[0] : false ;
        }
    }

}
//var_dump(Publication::publc_by_year());
?>