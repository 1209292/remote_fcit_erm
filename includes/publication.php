<?php


/**
 *
 */
class Publication
{
    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @var int
     */
    private $publication_id;

    /**
     * @var String
     */
    private $auther;

    /**
     * @var Date
     */
    private $publishing_date;

    /**
     * @var String
     */
    private $journal;

    /**
     * @var String
     */
    private $tilte;

    /**
     * @param void $String
     * @return String
     */
    public static function find_publication_by_author($String):String
    {
        // TODO: implement here
        return null;
    }

    /**
     * @param void $String
     * @return String
     */
    public static function find_publication_by_issue($String):String
    {
        // TODO: implement here
        return null;
    }

    /**
     * @param void $File
     * @return bool
     */
    public function upload_publication($File):bool
    {
        // TODO: implement here
        return false;
    }

    /**
     * @param void $int
     * @return bool
     */
    public function delete_publication($int):bool
    {
        // TODO: implement here
        return false;
    }

    /**
     * @param void $String
     * @param void $String
     * @return String
     */
    public function auto_search($String, $String):String
    {
        // TODO: implement here
        return null;
    }

    /**
     * @param void $String
     * @param void $String
     * @param void $String
     * @param void $String
     * @return String
     */
    public function manual_search($String, $String, $String, $String):String
    {
        // TODO: implement here
        return null;
    }
}
?>