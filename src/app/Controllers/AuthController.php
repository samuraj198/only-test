<?php
require_once __DIR__ . '/../Services/UserService.php';
require_once __DIR__ . '/../../db/mysql_connection.php';

class AuthController {
    private $userService;

    public function __construct() {
        global $pdo;

        $this->userService = new UserService($pdo);
    }

    public function register(array $data) {
        $registered = $this->userService->register($data);

        if ($registered) {
            header('Location: /profile');
        } else {
            header('Location: /register');
        }
    }

    public function login(array $data) {
        $registered = $this->userService->login($data);

        if ($registered) {
            header('Location: /profile');
        } else {
            header('Location: /login');
        }
    }

    public function logout() {
        $this->userService->logout();

        header('Location: /');
    }
}

$_SESSION['validation_errors'] = [];
$controller = new AuthController();
switch ($_POST['action']) {
    case 'register':
        $controller->register($_POST);
        break;
    case 'login':
        $controller->login($_POST);
        break;
    case 'logout':
        $controller->logout();
        break;
}