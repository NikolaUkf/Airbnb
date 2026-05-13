<?php
session_start();
include_once('connection.php');

$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $username = trim($_POST["username"]);
    $email    = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm  = $_POST["confirm_password"];

    // Validácia
    if (empty($username)) {
        $errors[] = "Zadajte užívateľské meno.";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Zadajte platný email.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Heslo musí mať aspoň 6 znakov.";
    }
    if ($password !== $confirm) {
        $errors[] = "Heslá sa nezhodujú.";
    }

    // Skontroluj či username alebo email už existuje
    if (empty($errors)) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->fetch()) {
            $errors[] = "Užívateľské meno alebo email už existuje.";
        }
    }

    // Vlož do databázy
    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$username, $email, $hashed]);
        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="sk">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registrácia</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="login.css">
</head>
<body>
<?php
require_once '../partials/head.php';
?>
  <main>
    <div class="card">
      <h1>Registrácia</h1>
      <p class="subtitle">Vytvorte si účet</p>

      <?php if ($success): ?>
        <div class="error-msg" id="errorMsg" role="alert" style="background: #e6f4ea; color: #2d7a3a; border-color: #2d7a3a;">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
            <circle cx="8" cy="8" r="7.25" stroke="currentColor" stroke-width="1.5"/>
            <polyline points="5,8 7,10 11,6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span>Registrácia úspešná! <a href="login.php">Prihláste sa</a></span>
        </div>
      <?php endif; ?>

      <?php if (!empty($errors)): ?>
        <div class="error-msg" id="errorMsg" role="alert">
          <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
            <circle cx="8" cy="8" r="7.25" stroke="currentColor" stroke-width="1.5"/>
            <line x1="8" y1="4.5" x2="8" y2="8.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
            <circle cx="8" cy="11" r=".85" fill="currentColor"/>
          </svg>
          <span><?php echo $errors[0]; ?></span>
        </div>
      <?php endif; ?>

      <form action="register.php" method="post" id="registerForm">

        <div class="field">
          <label for="username">Užívateľské meno</label>
          <input type="text" id="username" name="username" placeholder="Užívateľské meno"
                 value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"/>
        </div>

        <div class="field">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" placeholder="vas@email.com"
                 value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"/>
        </div>

        <div class="field">
          <label for="password">Heslo</label>
          <div class="input-wrap">
            <input type="password" id="password" name="password" placeholder="••••••••"/>
            <button class="toggle-pw" type="button" id="togglePw1" aria-label="Show password">
              <svg id="eyeOff1" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
              </svg>
              <svg id="eyeOn1" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="field">
          <label for="confirm_password">Potvrďte heslo</label>
          <div class="input-wrap">
            <input type="password" id="confirm_password" name="confirm_password" placeholder="••••••••"/>
            <button class="toggle-pw" type="button" id="togglePw2" aria-label="Show password">
              <svg id="eyeOff2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
              </svg>
              <svg id="eyeOn2" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
          </div>
        </div>

        <button class="btn-primary" type="submit">Registrovať sa</button>

      </form>

      <div class="divider">Máte už účet?</div>
      <a href="login.php"><button class="btn-secondary" type="button">Prihlásiť sa</button></a>

    </div>
  </main>

  <script>
    function togglePassword(btnId, inputId, eyeOffId, eyeOnId) {
      document.getElementById(btnId).addEventListener('click', () => {
        const input = document.getElementById(inputId);
        const shown = input.type === 'text';
        input.type = shown ? 'password' : 'text';
        document.getElementById(eyeOffId).style.display = shown ? '' : 'none';
        document.getElementById(eyeOnId).style.display  = shown ? 'none' : '';
      });
    }

    togglePassword('togglePw1', 'password',         'eyeOff1', 'eyeOn1');
    togglePassword('togglePw2', 'confirm_password',  'eyeOff2', 'eyeOn2');
  </script>
</body>
</html>