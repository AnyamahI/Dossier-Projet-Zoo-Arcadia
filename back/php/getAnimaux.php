<?php


function getAnimals(PDO $pdo):array
{
    $query = $pdo->prepare("SELECT * FROM animals");
    $query->execute();
    
        return $query->fetchAll(PDO::FETCH_ASSOC);

}