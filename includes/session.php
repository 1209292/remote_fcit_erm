<?php
/*
 * A class to help work with Sessions
 * In our case, primarily to manage ligging in & out
 */

require_once("member.php");
require_once("admin.php");
require_once("super_user.php");
class Session{

    private $member_logged_in = false;
    private $admin_logged_in = false;
    private $super_user_logged_in = false;
    public $admin_id;
    public $super_user_id;
    public $member_id;
    public $message;

    function __construct()
    {
        session_start();
        $this->check_message();
        $this->check_login();
    }

    public function is_logged_in($user_type=""){
        if($user_type == "member") {
            return isset($this->member_id) ? true : false ;
        } elseif ($user_type == "admin") {
            return isset($this->admin_id) ? true : false ;
            } elseif ($user_type == "super_user"){
            return isset($this->super_user_id) ? true : false ;
        } else {   return false;  }
    }

    public function find_id(){
        if($this->member_logged_in){
            return $this->member_id;
        }elseif($this->admin_logged_in){
            return $this->admin_id;
        }elseif($this->super_user_logged_in){
            return $this->super_user_id;
        }else{ return false; }
    }

    public function login($user){
        // DB should find user based on username/password
        if($user){
            if(is_a($user, "Member")){$this->member_id = $_SESSION['member_id'] = $user->id;  }
            elseif(is_a($user, "Admin")){ $this->admin_id = $_SESSION['admin_id'] = $user->id; }
            elseif(is_a($user, "SuperUser")){ $this->super_user_id = $_SESSION['super_user_id'] = $user->id; }
            else{
                $this->member_id = 0;
                $this->admin_id = 0;
                $this->super_user_id = 0;
                $this->message("This is session class");
            }
        }
    }

    public function logout(){
        if($this->member_logged_in) {
            unset($_SESSION['member_id']);
            unset($this->member_id);
            $this->member_logged_in = false;
        }elseif($this->admin_logged_in){
            unset($_SESSION['admin_id']);
            unset($this->admin_id);
            $this->admin_logged_in = false;
        }elseif($this->super_user_id){
            unset($_SESSION['super_user_id']);
            unset($this->super_user_id);
            $this->super_user_logged_in = false;
        }else{
            // Do nothing
            $this->message("This is session class");
        }
    }

    private function check_login()
    {
        if (isset($_SESSION['member_id'])) {
            $this->member_id = $_SESSION['member_id'];
            $this->member_logged_in = true;
        }elseif(isset($_SESSION['admin_id'])){
            $this->admin_id = $_SESSION['admin_id'];
            $this->admin_logged_in = true;
        }elseif(isset($_SESSION['super_user_id'])){
            $this->super_user_id = $_SESSION['super_user_id'];
            $this->super_user_logged_in = true;
        }else{
            unset($this->member_id);
            unset($this->admin_id);
            unset($this->super_user_id);
            $this->member_logged_in = false;
            $this->admin_logged_in = false;
            $this->super_user_logged_in = false;
        }
    }


    private function check_message(){
    // Is there a message stored in the session?
    if(isset($_SESSION['message'])){
        // Add it as an attribute and erase the stored vesion
        $this->message = $_SESSION['message'];
        unset($_SESSION['message']);
    }else{
        $this->message = "";
    }
}

    public function message($msg=""){
        if(!empty($msg)){
            // write a message
            $_SESSION['message'] = $msg;
        }else{
            // then this is "get message"
            return $this->message;
        }
    }

    public function set_member_use($member_id){
        $this->member_use = $member_id;
    }
}

$session = new Session();
$message = $session->message();

?>