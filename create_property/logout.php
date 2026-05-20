<?php

class Logout
{
    public function handle(): void
    {
        session_start();
        session_unset();
        session_destroy();
        header("Location: ../login_system/login.php");
        exit;
    }
}

(new Logout())->handle();