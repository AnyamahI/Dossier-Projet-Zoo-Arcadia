<?php
function verifyUserLoginPassword(PDO $pdo, string $email, string $password): bool|array
{
    $query = $pdo->prepare("SELECT * FROM users WHERE email = :email");
    $query->bindValue(':email', $email, PDO::PARAM_STR);
    $query->execute();

    $user = $query->fetch();
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;

    if ($user && password_verify($password, $user['password'])) {
        if ($user['role'] !== 'admin') {
            return false; 
        }
        return $user;
    }
}
