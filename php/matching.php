<?php
session_start();
require("../include.html");
require("classes/Database.php");
require("classes/User.php");

$db = new Database();
$user = new User();

if(array_key_exists('logged', $_SESSION) && $_SESSION['logged']){
    $user->setUser($_SESSION['uid']);
} else {
    header("location: /login");
}

include("../layout/nav.php");
?>
<link href="../css/movies.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"></script>

<div class="container">
    <div class="row">
        <div id="board">
            <div class="card"></div>
            <div class="card"></div>
        </div>
    </div>
</div>

<script src="/js/classes/Carousel.js"></script>