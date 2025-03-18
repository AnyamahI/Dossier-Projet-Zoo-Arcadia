<?php
ob_start(); // Active la mise en tampon de sortie
require_once __DIR__ . '/../lib/pdo.php';

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 3600,
        'path' => '/',
        'domain' => 'arcadia.local',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true
    ]);
    session_start();

    if (!isset($_SESSION['ip']) || $_SESSION['ip'] !== $_SERVER['REMOTE_ADDR']) {
        session_destroy();
        header('Location: login.php');
        exit();
    }
}

function isUserConnected(): bool
{
    return isset($_SESSION['user']);
}

function isAdmin(): bool
{
    return isUserConnected() && $_SESSION['user']['role'] === 'admin';
}

function isEmployee()
{
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'employee';
}

function isVeterinaire(): bool
{
    return isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'veterinaire';
}

function hasPermission(PDO $pdo, string $role, string $resource, string $action): bool
{
    $validActions = ['can_create', 'can_read', 'can_update', 'can_delete'];

    // Vérifier que l'action demandée est valide
    if (!in_array($action, $validActions)) {
        return false;
    }

    // Récupérer les permissions du rôle pour cette ressource
    $query = $pdo->prepare("
        SELECT $action 
        FROM permissions 
        WHERE role = :role AND resource = :resource
    ");
    $query->execute([
        ':role' => $role,
        ':resource' => $resource
    ]);

    $result = $query->fetch(PDO::FETCH_ASSOC);

    return $result && $result[$action] == 1;
}
