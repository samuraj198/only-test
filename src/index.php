<?php

$url = $_SERVER['REQUEST_URI'];

switch ($url) {
    case "/":
        include "pages/home.php";
        break;
    case "/register":
        include "pages/register.php";
        break;
    case "/login":
        include "pages/login.php";
        break;
    default:
        include "pages/404.php";
        break;
}