<?php
session_start();
require("../include.html");
require("classes/Database.php");
require("classes/User.php");
$db = new Database();
$user = new User();

if($_POST){
    $error = false;
    switch($_POST['type']){
        case 'register':
            if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
               $error = true;
               $message = "Email is niet acceptabel."; 
            } else if($user->validateUsername($_POST['username'])){
                $error = true;
                $message = "Username al in gebruik"; 
            }
            
            $username = $user->clearText($_POST['username']);
            $password = $user->clearPassword($_POST['password']);

            if(!$error){
                $insert = $user->insertNewUser($username, $password, $_POST['email']);
                if($insert == NULL){
                    // header('Location: /login');
                } 
            }
            break;
        case 'login':
            $username = $user->clearText($_POST['username']);
            $password = $user->clearPassword($_POST['password']);

            $username_check = $user->validateUsername($username);
            if($username_check){
                if(password_verify($password, $username_check[0]['password'])){
                    $_SESSION['username'] = $username;
                    $_SESSION['uid'] = $username_check[0]['id'];
                    $_SESSION['email'] = $username_check[0]['email'];
                    $_SESSION['logged'] = true;

                    header("Location: /");
                } else {
                    $message_login = "Wachtwoord klopt niet.";
                }
            } else {
                $message_login = "Username bestaat niet.";
            }
            break;
    }
}

?>
<link href="/css/loginRegister.css" rel="stylesheet" />

<div class="login-page">
    <div class="form">
        <form action="" method="post" class="register-form">
            <input type="hidden" name="type" value="register">
            <input required name="username" type="text" placeholder="name"/>
            <input required name="password" type="password" placeholder="password"/>
            <input required name="email" type="email" placeholder="email address"/>
            <button type="submit">create</button>
            <p class="message">Already registered? <a href="#">Sign In</a></p>
        </form>
        
        <form method="post" class="login-form">
            <input type="hidden" name="type" value="login">
            <input required name="username" type="text" placeholder="username"/>
            <input required name="password" type="password" placeholder="password"/>
            <button type="submit">login</button>
            <p class="message">Not registered? <a href="#">Create an account</a></p>
        </form>
    </div>
</div>

<script src="/js/loginRegister.js"></script>