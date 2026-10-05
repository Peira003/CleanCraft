<?php

session_start();

require_once 'config/configDB.php';

if(isset($_SESSION['logged_id'])){
    header('Location: /views/index.php');
} else {
    header('Location: /views/login.php');
    exit();
}

if(isset($_POST['mail'])){

}