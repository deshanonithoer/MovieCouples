<?php
session_start();
require("include.html");
require("php/classes/Database.php");
require("php/classes/User.php");
$db = new Database();

include("layout/nav.php");
?>
<!DOCTYPE html>
<link href="/css/home.css" rel="stylesheet" />

<div class="container">
    <div class="row">
        <h1 style="margin: auto; padding-top: 100px; color: #9e9e9e;">Framework - Shano Nithoer</h1>
    </div>
</div>

<div id="info-box" class="alert alert-success" role="alert"></div>
<script src="/js/home.js"></script>
