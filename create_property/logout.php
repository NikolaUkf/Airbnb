<?php
session_start();

// vymaže všetky session premenné
session_unset();

// zničí session
session_destroy();

// presmerovanie na login
header("Location: ../login_system/login.php");
exit;