<?php
$userName = '';

if(array_key_exists('logged', $_SESSION) && $_SESSION['logged']){
    $user = new User();
    $user->setUser($_SESSION['uid']);
} 

?>
<!DOCTYPE html>
<link rel="stylesheet" href="../css/nav.css">

<nav class="container">
    <div class="row">
        <div class="col-md-3 mr-auto">
            <a href="/">
                <button type="button" class="btn btn-info">
                    <i class="fas fa-home"></i> 
                </button>
            </a>

            <?php if (array_key_exists('logged', $_SESSION) && $_SESSION['logged']) { ?>
                <a href="/matching">
                    <button type="button" class="btn btn-info">
                        Movies 
                    </button>
                </a>
            <?php } ?>
            
            <?php if (array_key_exists('logged', $_SESSION) && $_SESSION['logged']) { ?>
                <a href="/users">
                    <button type="button" class="btn btn-info">
                        <i class="fas fa-users"></i> 
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
                        <i class="far fa-user-circle"></i>
                    </button>
                </a>
                <a href="/loguit">
                    <button type="button" class="btn btn-info">
                        <i class="fas fa-sign-out-alt"></i>
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