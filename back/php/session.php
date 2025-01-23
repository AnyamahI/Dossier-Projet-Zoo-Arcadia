<?php

session_set_cookie_params([
    'lifetime' => 3600,
    'path' => '/',
    'domain' => '.arcadia.local',
    'httponly' => true
]);

session_start();

function isUserConnected():bool
{
    return isset($_SESSION['user']);
}