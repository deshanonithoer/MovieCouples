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
            <div class="card" no-card>Start!</div>
        </div>
    </div>
</div>

<!-- Modal users -->
<div class="modal" id="movie-modal-container" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Movie Info</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body" id="movie-modal">
            
        </div>
        <div class="modal-footer">

        </div>
        </div>
    </div>
</div>

<script src="/js/classes/Carousel.js"></script>