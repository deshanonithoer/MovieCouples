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
<link href="../css/profile.css" rel="stylesheet" />

<div class="container">
    <form id="user-info" action="/php/data/profile.php" method="post">
        <div class="row">
            <div class="col-md-5">
                <!-- Profile image -->
                <div id="profile-image">
                    <?php if($user->image_path != NULL) { 
                        echo '<img src="' . $user->image_path . '" alt="Profile Image">';
                        $textImage = "Verander je huidige foto";
                    } else { ?>
                        <img src="https://lunawood.com/wp-content/uploads/2018/02/placeholder-image.png" alt="placeholder image">
                    <?php $textImage = "Voeg een foto toe"; } ?>
                    <div class="custom-file">
                        <input name="profile_image" type="file" class="custom-file-input" id="image-uploader">
                        <label class="custom-file-label" for="customFileLangHTML" data-browse="Bestand kiezen"><?= $textImage ?></label>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div id="profile-info">
                    <!-- Name -->
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-default">Name</span>
                        </div>
                        <input name="username" type="text" class="form-control" value="<?= ucwords($user->name) ?>" aria-label="Name" aria-describedby="inputGroup-sizing-default">
                    </div>

                    <!-- Email -->
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="inputGroup-sizing-default">Email</span>
                        </div>
                        <input name="ëmail" type="text" class="form-control" value="<?= $user->email ?>" aria-label="Email" aria-describedby="inputGroup-sizing-default">
                    </div>

                    <!-- Gender -->
                    <div class="form-check form-check-inline">
                        <input class="form-check-input gender" type="radio" name="gender" id="inlineRadio1" value="0" <?php echo ($user->gender != NULL && $user->gender == "0") ? "checked" : "" ; ?>>
                        <label class="form-check-label" for="inlineRadio1">Man</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input gender" type="radio" name="gender" id="inlineRadio2" value="1" <?php echo ($user->gender != NULL && $user->gender == "1") ? "checked" : "" ; ?>>
                        <label class="form-check-label" for="inlineRadio2">Vrouw</label>
                    </div>

                    <button type="submit" style="display: block; margin-top: 15px;" type="button" class="btn btn-success">
                        Update 
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<div id="info-box" class="alert alert-success" role="alert"></div>

<script src="/js/classes/Validate.js"></script>
<script src="/js/classes/Form.js"></script>
<script src="/js/profile.js"></script>