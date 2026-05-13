<?php
session_start();
$loginError = isset($_GET['error']) ? true : false;

// Ak je už prihlásený, presmeruj rovno
if (isset($_SESSION['admin'])) {
    header("Location: ../create_property/create.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Log in</title>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&family=DM+Serif+Display&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="login.css">
</head>
<body>
<?php
$pageTitle = 'Prihlásenie';
require_once '../partials/head.php';
?>
  <main>
    <div class="card">
      <h1>Vitajte</h1>
      <p class="subtitle">Prihlásenie</p>

      <!-- Error message -->
      <div class="error-msg <?php echo $loginError ? '' : 'hidden'; ?>" id="errorMsg" role="alert">
        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
          <circle cx="8" cy="8" r="7.25" stroke="currentColor" stroke-width="1.5"/>
          <line x1="8" y1="4.5" x2="8" y2="8.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
          <circle cx="8" cy="11" r=".85" fill="currentColor"/>
        </svg>
        <span id="errorText">Nesprávne prihlasovacie údaje.</span>
      </div>

      <form action="user_validate.php" method="post" id="loginForm">

        <div class="field">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" placeholder="Username" autocomplete="username"/>
        </div>

        <div class="field">
          <label for="password">Password</label>
          <div class="input-wrap">
            <input type="password" id="password" name="password" placeholder="••••••••" autocomplete="current-password"/>
            <button class="toggle-pw" type="button" id="togglePw" aria-label="Show password">
              <svg id="eyeOff" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/>
                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
              </svg>
              <svg id="eyeOn" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" style="display:none">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
            </button>
          </div>
        </div>

        <div class="forgot-row">
          <a href="#">Forgot your password? <span>Reset</span></a>
        </div>

        <button class="btn-primary" type="submit" name="login" id="loginBtn">Log in</button>

      </form>

      <div class="divider">New to Optiflow?</div>
      
      <button class="btn-secondary" type="button" onclick="window.location.href='register.php'">Sign up</button>
    </div>
  </main>


  <script>
    const usernameEl = document.getElementById('username');
    const passwordEl = document.getElementById('password');
    const loginForm  = document.getElementById('loginForm');
    const errorMsg   = document.getElementById('errorMsg');
    const errorText  = document.getElementById('errorText');
    const togglePw   = document.getElementById('togglePw');
    const eyeOff     = document.getElementById('eyeOff');
    const eyeOn      = document.getElementById('eyeOn');

    togglePw.addEventListener('click', () => {
      const shown = passwordEl.type === 'text';
      passwordEl.type = shown ? 'password' : 'text';
      eyeOff.style.display = shown ? '' : 'none';
      eyeOn.style.display  = shown ? 'none' : '';
    });

    [usernameEl, passwordEl].forEach(el => {
      el.addEventListener('input', () => {
        el.classList.remove('error-field');
        hideError();
      });
    });

    function showError(msg) {
      errorText.textContent = msg;
      errorMsg.classList.remove('hidden');
    }

    function hideError() {
      if (!usernameEl.classList.contains('error-field') && !passwordEl.classList.contains('error-field')) {
        errorMsg.classList.add('hidden');
      }
    }

    function validate() {
      if (!usernameEl.value.trim()) {
        usernameEl.classList.add('error-field');
        showError('Zadajte prihlasovacie meno.');
        usernameEl.focus();
        return false;
      }
      if (!passwordEl.value) {
        passwordEl.classList.add('error-field');
        showError('Zadajte heslo.');
        passwordEl.focus();
        return false;
      }
      return true;
    }

    loginForm.addEventListener('submit', (e) => {
      if (!validate()) {
        e.preventDefault();
      }
    });
  </script>
</body>
</html>