<?php
session_start();
if(!array_key_exists('logged', $_SESSION) || $_SESSION['logged'] == false){
    header("location: /login");
} 

require("../include.html");
require("classes/Database.php");
require("classes/User.php");
$db = new Database();

include("../layout/nav.php");

?>
<link href="../css/users.css" rel="stylesheet" />

<div class="container friends-container">
    <div class="row">
        <div class="col-md-3 text-center">
            <button type="button" value="fetchUsers" class="btn friends-nav-button btn-info users-button">
                Users
            </button>
            <button type="button" value="fetchFriends" class="btn friends-nav-button btn-success">
                Friends 
            </button>
            <button type="button" value="fetchRequests" class="btn friends-nav-button btn-warning">
                Requests <span class="badge badge-light friend-requests-counter"><?= (property_exists($user, 'totalRequests') && $user->totalRequests) ? $user->totalRequests : '' ; ?></span>
            </button>
        </div>

        <div class="col-md-9" id="friends-content">
            <div class="input-group mb-3">
                <input type="text" id="search-friends" class="form-control" placeholder="Recipient's username" aria-label="Recipient's username" aria-describedby="basic-addon2">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary" type="button">Zoek</button>
                </div>
            </div>
            <div class="row no-gutters" id="users-wrapper"></div>
        </div>
    </div>
</div>

<!-- Modal users -->
<div class="modal" id="user-modal-container" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title">Send friend request</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="modal-body" id="user-modal">
            
        </div>
        <div class="modal-footer">
            <button type="button" id="send-friend-request" class="btn btn-success" data-dismiss="modal">Send friend request</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
        </div>
    </div>
</div>

<script src="../js/classes/Form.js"></script>
<script src="../js/classes/Users.js"></script>