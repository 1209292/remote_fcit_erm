<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 11/04/2016
 * Time: 02:31 pm
 */
require_once ("../../includes/database.php");
require_once ("../../includes/member.php");
require_once ("../../includes/session.php");
require_once ("../../includes/functions.php");
require_once ("../../includes/publication.php");

 if(!$session->is_logged_in('member')){redirect_to("../login.php"); }
 $member = Member::find_by_id($session->find_id());
 if(isset($_GET['p_id'])){
     /*ckecks for int only, return value on success, false on fail, null if GET not set*/
     $valid_int_value = filter_input(INPUT_GET, 'p_id', FILTER_VALIDATE_INT);
     if($valid_int_value){
        $publication = Publication::find_by_id($valid_int_value);
         if($publication){
             $result = $publication->delete($valid_int_value);
             if($result){
                 $session->message("Publication was deleted successfully.");
                 redirect_to("public.php");
             }else{
                 $session->message("Publication couldn't be deleted.");
                 redirect_to("public.php");
             }
         }else{
             $session->message("Choose one of the publication to delete.");
             redirect_to("public.php");
         }
     }else{
         redirect_to("public.php");
     }
 }