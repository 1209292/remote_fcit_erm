<?php
require_once("config.php");

class MySQLDatabase
{
    private $connection;

    function __construct()
    {
        $this->open_connection();
    }

    public function open_connection()
    {
        $this->connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
        if (mysqli_connect_errno()) {
            // die meant stop, kill everything and quit
            die("DB connection failed: " .
                mysqli_connect_error() .
                "(" . mysqli_connect_errno() . ")"
            );
        }
    }

    public function close_connection()
    {
        if (isset($this->connection)) {
            mysqli_close($this->connection);
            unset($this->connection);
        }
    }

    public function query($sql)
    {
        $result = mysqli_query($this->connection, $sql);
        $this->confirm_query($result);
        return $result;
    }

    public function escape_value($string)
    {
        $escaped_string = mysqli_real_escape_string($this->connection, $string);
        return $escaped_string;
    }


    private function confirm_query($result_set)
    {
        if (!$result_set) {
            die("DB query failed ...");
        }
    }

    // database neutral functions
    /*what we have done here is just making the functions names general so if use other DB we can use the same naming*/
    public function fetch_array($result_set)
    {
        /* we can replace the query depending on the DB we use, so this function will fetch the array
        useing postgrez DB or mysqli DB, we give it a name that is not dependent on the DB
        apply the same concept for every DB function, do it as you can*/
        return mysqli_fetch_array($result_set);
    }

    public function num_rows($result_set)
    {
        return mysqli_num_rows($result_set);
    }

    public function insert_id()
    {
        // get the last id inserted over the current database connection
        return mysqli_insert_id($this->connection);
    }

    public function affected_rows()
    {
        return mysqli_affected_rows($this->connection);
    }

    function prepare($string)
    {
        return mysqli_real_escape_string($this, $string);
    }

}

$database = new MySQLDatabase();

?>