<?php
    // start session if session is not here.
    if(session_id()==''){
        session_start();
    }
    
     if(!isset($_SESSION['password'])){
        $_SESSION['password']='';
     }
    
    // redirect is $_SESSION['user_id'] is not set
    if(!isset($_SESSION['user_id'])){
        $_SESSION['user_id']='guest';
        header('Location: '.'login.php');
        exit;
    }
?>