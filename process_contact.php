<?php
// SMTP konfigurácia (uprav podľa tvojej služby)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'your-email@gmail.com');
define('SMTP_PASS', 'your-password');
define('FROM_EMAIL', 'info@villa.co');
define('ADMIN_EMAIL', 'admin@villa.co');

// Očista a validácia vstupu
function sanitizeInput($input) {
    return trim(htmlspecialchars($input, ENT_QUOTES, 'UTF-8'));
}

// Validácia emailu
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Spracovanie formulára
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = array('success' => false, 'message' => '');

    // Získaj a očisti dáta
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');

    // Validácia
    if (empty($name)) {
        $response['message'] = 'Meno je povinné.';
    } elseif (empty($email) || !isValidEmail($email)) {
        $response['message'] = 'Zadaj platnú emailovú adresu.';
    } elseif (empty($message)) {
        $response['message'] = 'Správa je povinná.';
    } else {
        // Odoslanie emailu správcovi
        $adminSubject = "Nová správa z kontaktného formulára: " . $subject;
        $adminMessage = "Meno: " . $name . "\n";
        $adminMessage .= "Email: " . $email . "\n";
        $adminMessage .= "Predmet: " . $subject . "\n\n";
        $adminMessage .= "Správa:\n" . $message;

        $headers = "From: " . $email . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        // Odoslanie emailu užívateľovi (potvrdenie)
        $userSubject = "Potvrdenie prijatia vašej správy - Villa Agency";
        $userMessage = "Ďakujeme za vašu správu, " . $name . "!\n\n";
        $userMessage .= "Vaša správa bola úspešne prijatá. Náš tím sa vám ozve v najkratšom čase.\n\n";
        $userMessage .= "S pozdravom,\nVilla Agency";

        $userHeaders = "From: " . FROM_EMAIL . "\r\n";
        $userHeaders .= "Content-Type: text/plain; charset=UTF-8\r\n";

        // Pokusy odoslať emaily
        $adminSent = mail(ADMIN_EMAIL, $adminSubject, $adminMessage, $headers);
        $userSent = mail($email, $userSubject, $userMessage, $userHeaders);

        if ($adminSent && $userSent) {
            $response['success'] = true;
            $response['message'] = 'Ďakujeme! Vaša správa bola úspešne odoslaná.';
        } else {
            $response['message'] = 'Vyskytla sa chyba pri odosielaní. Skúste neskôr.';
        }
    }

    // Vrátenie JSON odpovede
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
?>