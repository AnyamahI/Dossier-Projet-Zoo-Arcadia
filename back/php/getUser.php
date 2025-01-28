<?php


function getUser(PDO $pdo):array
{
    $query = $pdo->prepare("SELECT * FROM users");
    $query->execute();
    
        return $query->fetchAll(PDO::FETCH_ASSOC);

}