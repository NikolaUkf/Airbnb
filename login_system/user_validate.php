<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/Airbnb/login_system/connection.php');

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = test_input($_POST["username"]);
    $password = $_POST["password"];

    // Najprv skontroluj admina
    $stmt = $conn->prepare("SELECT * FROM adminlogin WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin'] = $admin['username'];
        header("Location: ../create_property/create.php");
        exit();
    }

    // Potom skontroluj bežného užívateľa
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']       = $user['id'];
        $_SESSION['user_username'] = $user['username'];
        $_SESSION['user_email']    = $user['email'];
        header("Location: ../index.php");
        exit();
    }

    // Ani jedno nesedí
    header("Location: login.php?error=1");
    exit();
}
?>