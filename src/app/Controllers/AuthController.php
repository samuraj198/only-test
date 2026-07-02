<?php
require_once __DIR__ . '/../Services/UserService.php';
require_once __DIR__ . '/../../db/mysql_connection.php';

class AuthController {
    private UserService $userService;

    public function __construct() {
        global $pdo;

        $this->userService = new UserService($pdo);
    }

    public function register(array $data): void {
        $registered = $this->userService->register($data);

        if ($registered) {
            header('Location: /profile');
        } else {
            header('Location: /register');
        }

        exit;
    }

    public function login(array $data): void {
        $registered = $this->userService->login($data);

        if ($registered) {
            header('Location: /profile');
        } else {
            header('Location: /login');
        }

        exit;
    }

    public function logout(): void {
        $this->userService->logout();

        header('Location: /');
        exit;
    }

    public function updateData(array $data): void
    {
        $this->userService->update($data);

        header('Location: /profile');
        exit;
    }

    public function changePassword(array $data): void
    {
        $this->userService->changePassword($data);

        header('Location: /profile');
        exit;
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
    case 'updateData':
        $controller->updateData($_POST);
        break;
    case 'changePassword':
        $controller->changePassword($_POST);
        break;
}