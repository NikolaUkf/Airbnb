<?php
$pageTitle = $pageTitle ?? '';
$pageStyles = $pageStyles ?? [];
?>
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ? $pageTitle . ' | Villa' : 'Villa'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700;800&family=Open+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Airbnb/assets/css/head.css">
    <?php foreach ($pageStyles as $style): ?>
        <link rel="stylesheet" href="/Airbnb/<?php echo $style; ?>.css">
    <?php endforeach; ?>
</head>
<body>

<div class="top-bar">
    <div class="top-bar__inner">
        <div class="top-bar__contacts">
            <a href="mailto:info@company.com" class="top-bar__contact-item">
                <svg class="icon icon-email" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 4H4C2.897 4 2 4.897 2 6v12c0 1.103.897 2 2 2h16c1.103 0 2-.897 2-2V6c0-1.103-.897-2-2-2zm0 2-8 5-8-5h16zm0 12H4V8.868l8 5 8-5V18z"/>
                </svg>
                <span>info@company.com</span>
            </a>
            <div class="top-bar__divider"></div>
            <a href="https://maps.google.com/?q=Sunny+Isles+Beach+FL+33160" target="_blank" rel="noopener" class="top-bar__contact-item">
                <svg class="icon icon-map" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5C10.62 11.5 9.5 10.38 9.5 9S10.62 6.5 12 6.5 14.5 7.62 14.5 9 13.38 11.5 12 11.5z"/>
                </svg>
                <span>Sunny Isles Beach, FL 33160</span>
            </a>
        </div>
        <div class="top-bar__socials">
            <a href="#" class="social-btn" aria-label="Facebook">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
            </a>
            <a href="#" class="social-btn" aria-label="Twitter">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg>
            </a>
            <a href="#" class="social-btn" aria-label="LinkedIn">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/></svg>
            </a>
            <a href="#" class="social-btn" aria-label="Instagram">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                </svg>
            </a>
        </div>
    </div>
</div>

<header class="main-header">
    <div class="main-header__inner">
        <a href="/Airbnb/index.php" class="logo">Villa</a>
        <nav id="main-nav" aria-label="Hlavná navigácia">
            <ul class="main-nav">
                <li>
                    <a href="/Airbnb/index.php"
                       class="main-nav__link <?php echo ($_SERVER['PHP_SELF'] === '/Airbnb/index.php') ? 'active' : ''; ?>">
                        Domov
                    </a>
                </li>
                <li>
                    <a href="/Airbnb/properties.php"
                       class="main-nav__link <?php echo ($_SERVER['PHP_SELF'] === '/Airbnb/properties.php') ? 'active' : ''; ?>">
                        Akomodácie
                    </a>
                </li>
                <li>
                    <a href="/Airbnb/contact.php"
                       class="main-nav__link <?php echo ($_SERVER['PHP_SELF'] === '/Airbnb/contact.php') ? 'active' : ''; ?>">
                        Kontakt
                    </a>
                </li>
                <li>
                    <a href="/Airbnb/login_system/login.php"
                       class="main-nav__link <?php echo ($_SERVER['PHP_SELF'] === '/Airbnb/login_system/login.php') ? 'active' : ''; ?>">
                        Prihlásenie
                    </a>
                </li>
            </ul>
        </nav>
        <button class="hamburger" id="hamburger" aria-label="Menu" aria-expanded="false" aria-controls="main-nav">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>
</header>

<script>
    const hamburger = document.getElementById('hamburger');
    const nav = document.querySelector('.main-nav');

    hamburger.addEventListener('click', () => {
        const isOpen = nav.classList.toggle('open');
        hamburger.setAttribute('aria-expanded', isOpen);
    });
</script>