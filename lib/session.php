<?php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 3600,
        'path' => '/',
        'domain' => 'arcadia.local',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true
    ]);
    session_start();
}

function isUserConnected(): bool
{
    return isset($_SESSION['user']);
}

function isAdmin(): bool
{
    return isUserConnected() && $_SESSION['user']['role'] === 'admin';
}

function isVeterinaire()
{

    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'veterinaire';
}

function isEmploye()
{

    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'employe';
}
