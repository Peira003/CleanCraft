<?php

session_start();

if($_SERVER['REQUEST_METHOD'] == "POST"){
    $mail = $_POST['email'];
    $password = $_POST['password'];

    if(empty($mail)){
        $_SESSION['error'] = "Wprowadź email";
        header("Location: login.html");
        exit();
    } else if(empty($password)) {
        $_SESSION['error'] = "Wprowadź hasło";
        header("Location: login.html");
        exit();
    } else {
        
    }

}