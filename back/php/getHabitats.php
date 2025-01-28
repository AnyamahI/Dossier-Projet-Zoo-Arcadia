<?php


function getHabitats(PDO $pdo):array
{
    $query = $pdo->prepare("SELECT * FROM habitats");
    $query->execute();
    
        return $query->fetchAll(PDO::FETCH_ASSOC);

}