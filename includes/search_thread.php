<?php

/**
 * Created by PhpStorm.
 * User: windows
 * Date: 04/05/2016
 * Time: 10:29 pm
 */
require_once "scholar_object.php";

class SearchThread // extends Thread
{
    public $member_full_name;
    public $member_id;
    function __construct($member_full_name, $member_id)
    {
        $this->member_full_name = $member_full_name;
        $this->member_id = $member_id;
    }

    public function run()
    {
        $result_set = ScholarObject::my_search($this->member_full_name);
        if($result_set){
            $result_set = ScholarObject::already_exists_in_publications($result_set, $this->member_id);
            /**** check already_exists_in_scholar() ****/
            if($result_set){ // publications found not existed before
                $count = count($result_set);
                for($i=0; $i<$count; $i++) { // save publications
                    ScholarObject::save(array_shift($result_set), $this->member_id);
                }
            }
        }
    }
}