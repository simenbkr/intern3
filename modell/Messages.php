<?php

namespace intern3;

class Messages{
    public static function setMsg($text, $type){
        if($type == 'error'){
            //$_SESSION['errorMsg'] = $text;
            setcookie('error',$text);
        } else {
            //$_SESSION['successMsg'] = $text;
            setcookie('success',$text);
        }
    }

    public static function display(){
        //if(isset($_SESSION['errorMsg'])){
        if (isset($_COOKIE['error'])){
            echo '<div class="alert alert-danger">'.$_COOKIE['errorMsg'].'</div>';
            unset($_COOKIE['error']);
        }

        //if(isset($_SESSION['successMsg'])){
        if (isset($_COOKIE['success'])){
            echo '<div class="alert alert-success">'.$_COOKIE['successMsg'].'</div>';
            unset($_COOKIE['success']);
        }
    }
}
?>