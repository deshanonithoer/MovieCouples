<?php
session_start();
include("../include.html");
require("classes/Database.php");

unset($_SESSION['username'], $_SESSION['email']);
$_SESSION['logged'] = false;
header("location: /");