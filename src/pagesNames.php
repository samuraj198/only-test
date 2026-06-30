<?php
function getPageNameForTitle(string $url) {
    $pagesName = [
        '/' => 'Главная',
        '/register' => 'Регистрация',
        '/login' => 'Авторизация',
        '/profile' => 'Профиль',
    ];

    return $pagesName[$url];
}