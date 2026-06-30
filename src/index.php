<?php
session_start();

require_once "db/mysql_connection.php";
include_once "pagesNames.php";

$url = $_SERVER['REQUEST_URI'];

if ($url === '/profile' && empty($_SESSION['user'])) {
    $_SESSION['auth_error'] = 'Чтобы попасть на страницу профиля необходимо авторизоваться';
    header('Location: /');
    exit();
}
?>

<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?=getPageNameForTitle($url)?></title>
</head>
<body>
<header>
    <?php
    if (empty($_SESSION['user'])) { ?>
    <a href="/register">Регистрация</a>
    <a href="/login">Авторизация</a>
    <?php } ?>
    <?php if (isset($_SESSION['user'])) { ?>
    <a href="/profile">Профиль</a>
    <form action="./app/Controllers/AuthController.php" method="POST">
        <input hidden type="text" name="action" value="logout">
        <button type="submit">Выйти</button>
    </form>
    <?php } ?>
</header>
<?php
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
    case "/profile":
        include "pages/profile.php";
        break;
    default:
        include "pages/404.php";
        break;
}
?>
</body>
</html>
