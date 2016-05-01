<?php

/**
 * Created by PhpStorm.
 * User: windows
 * Date: 20/04/2016
 * Time: 11:19 am
 */
class Pagination
{

    public $current_page;
    public $per_page;
    public $total_count;

    public function __construct($page=1, $per_page=20, $total_count=0){
        $this->current_page = (int) $page;
        $this->per_page = (int) $per_page;
        $this->total_count = $total_count;
    }

    public function offset(){
        /*
         * Assuming 20 items per page:
         * page 1 has an offset of 0 (1-1) * 20
         * page 2 has an offset of 20 (2-1) * 20
         * in other words, page 2 starts with item 21 */
        return ($this->current_page - 1) * $this->per_page;
    }

    public function total_pages(){
        /** we want to round up using ceil, otherwise there will be some pages left */
        return ceil($this->total_count / $this->per_page);
    }

    /** we can write extra checks, so we don't accidently go too low or too high in pages*/
    public function previous_page(){
        return $this->current_page - 1;
    }

    public function next_page(){
        return $this->current_page + 1;
    }

    public function has_previous_page(){
        return $this->previous_page() >= 1 ? true : false;
    }

    public function has_next_page(){
        return $this->next_page() <= $this->total_pages() ? true : false;
    }

}