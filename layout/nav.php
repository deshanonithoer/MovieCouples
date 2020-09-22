<?php
$userName = '';

if(array_key_exists('logged', $_SESSION) && $_SESSION['logged']){
    $user = new User();
    $user->setUser($_SESSION['uid']);
} 

?>
<link rel="stylesheet" href="../css/nav.css">

<nav class="container">
    <div class="row">
        <div class="col-md-3 mr-auto">
            <a href="/">
                <button type="button" class="btn btn-info">
                    Home 
                </button>
            </a>

            <?php if (array_key_exists('logged', $_SESSION) && $_SESSION['logged']) { ?>
                <a href="/users">
                    <button type="button" class="btn btn-info">
                        Users 
                    </button>
                </a>
            <?php } ?>
        </div>

        <?php if (array_key_exists('logged', $_SESSION) && $_SESSION['logged']) { ?>
            <input id="uid" type="hidden" name="uid" value="<?= $user->uid ?>">
            <div id="profile" class="ml-auto mr-3">
                <span class="welkom-message">Welkom <?= ucwords($user->name) ?>!</span>
                <a href="/profile">
                    <button type="button" class="btn btn-info">
                        Profile
                    </button>
                </a>
                <a href="/loguit">
                    <button type="button" class="btn btn-info">
                        Logout
                    </button>
                </a>
            </div>
        <?php } else { ?>
            <div id="profile" class="ml-auto mr-3">
                <a href="/login">
                    <button type="button" class="btn btn-info">
                        Login 
                    </button>
                </a>
            </div>
        <?php } ?>
    </div>
</nav>