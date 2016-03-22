<?php

// root functions, not part of a class

function strip_zeros_from_date($marked_string=""){
    // first remove the marked zeros
    $no_zeros = str_replace('*0', '', $marked_string);
    // then remove any remaining marks
    $cleaned_string = str_replace('*', '', $no_zeros);
    return $cleaned_string;
}

function redirect_to($location= NULL){
    if($location != NULL){
        header("Location: {$location}");
        exit;
    }
}

function output_message($message = ""){
    if(!empty($message)){
        return "<p class=\"message\">{$message}</p>";
    }else{
        return "";
    }
}

// defined function like __construct but outside the class
/* if PHP doesn't find a class it will stop and will look for __outoload to ask it what to do
 and PHP will pass the class name as an argument*/
function __autoload($class_name){
    $class_name = strtolower($class_name);
    $path = "C:/wamp/www/fcit_erm/includes/{$class_name}.php";
    if(file_exists($path)){
        require_once ($path);
    }else{
        die("The file {$class_name}.php couldn't be found.");
    }
}


// if we had a lot of layouts we could roll them into a class/
function include_layout_template($template=""){
    // help us to locate the layout wherever it was
 include(SITE_ROOT.DS.'public'.DS.'layouts'.DS.$template);
}

function datetime_to_text($datetime=""){
    $unixdatetime = strtotime($datetime); // to convert to a timestamp
    return strftime("%B %d, %Y at %I:%M %p", $unixdatetime); // then format it to nice way (you can have your own format
}
?>