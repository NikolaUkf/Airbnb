<?php
session_start(); 
include_once($_SERVER['DOCUMENT_ROOT'] . '/Airbnb/login_system/connection.php');

class AuthRepository
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function findAdmin(string $username): ?array
    {
        $stmt = $this->conn->prepare("SELECT username, password FROM adminlogin WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function findUser(string $username): ?array
    {
        $stmt = $this->conn->prepare("SELECT id, username, email, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}

class AuthController
{
    private AuthRepository $repository;

    public function __construct(AuthRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(): void
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;


        $username = trim($_POST["username"] ?? '');
        $password = $_POST["password"]      ?? '';


        if ($username === '' || $password === '') {
            header("Location: login.php?error=1");
            exit();
        }

        $admin = $this->repository->findAdmin($username);
        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true); // Ochrana pred session fixation
            $_SESSION['admin'] = $admin['username'];
            header("Location: ../create_property/create.php");
            exit();
        }

        $user = $this->repository->findUser($username);
        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true); // Ochrana pred session fixation
            $_SESSION['user_id']       = $user['id'];
            $_SESSION['user_username'] = $user['username'];
            $_SESSION['user_email']    = $user['email'];
            header("Location: ../index.php");
            exit();
        }

        header("Location: login.php?error=1");
        exit();
    }
}

$controller = new AuthController(new AuthRepository($conn));
$controller->handle();