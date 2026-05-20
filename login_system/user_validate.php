<?php
include_once($_SERVER['DOCUMENT_ROOT'] . '/Airbnb/login_system/connection.php');

class InputSanitizer
{
    public function clean(string $data): string
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }
}

class AuthRepository
{
    private PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    public function findAdmin(string $username): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM adminlogin WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch() ?: null;
    }

    public function findUser(string $username): ?array
    {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch() ?: null;
    }
}

class AuthController
{
    private AuthRepository $repository;
    private InputSanitizer $sanitizer;

    public function __construct(AuthRepository $repository, InputSanitizer $sanitizer)
    {
        $this->repository = $repository;
        $this->sanitizer  = $sanitizer;
    }

    public function handle(): void
    {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;

        $username = $this->sanitizer->clean($_POST["username"] ?? '');
        $password = $_POST["password"] ?? '';

        $admin = $this->repository->findAdmin($username);
        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin'] = $admin['username'];
            header("Location: ../create_property/create.php");
            exit();
        }

        $user = $this->repository->findUser($username);
        if ($user && password_verify($password, $user['password'])) {
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

$controller = new AuthController(new AuthRepository($conn), new InputSanitizer());
$controller->handle();