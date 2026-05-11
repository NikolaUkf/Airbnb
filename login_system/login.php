<?php
$pageTitle = 'Prihlásenie';
require_once '../partials/head.php';
?>
<link rel="stylesheet" href="login.css">
<div class="login-container">
    <form action="validate.php" method="post">
        <div class="login-box">
            <h1>Prihlásenie</h1>
            <div class="textbox">
                <input type="text" placeholder="Username" name="username">
            </div>
            <div class="textbox">
                <input type="password" placeholder="Password" name="password">
            </div>
            <input class="button" type="submit" name="login" value="Sign In">
        </div>
    </form>
</div>

<?php include '../partials/footer.php'; ?>