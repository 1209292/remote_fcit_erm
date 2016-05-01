<?php
/**
 * Created by PhpStorm.
 * User: windows
 * Date: 25/04/2016
 * Time: 02:07 am
 */

require_once ("../includes/database.php");
require_once ("../includes/member.php");
require_once ("../includes/session.php");
require_once ("../includes/functions.php");

if(!isset($_GET['publc_id'])){redirect_to("all_profiles.php");}
if ( filter_var($_GET['publc_id'], FILTER_VALIDATE_INT , array("min_range"=>1,"max_range"=>PHP_INT_MAX)) === false ) {
    redirect_to('all_profiles.php');
}
$publc = Publication::find_by_id($_GET['publc_id']);
if(!$publc){ redirect_to("all_profiles.php"); }
$hits = $publc->hits + 1;
$sql = "UPDATE publications SET hits = {$hits} WHERE id = " . $publc->id;
$database->query($sql);