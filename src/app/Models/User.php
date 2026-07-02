<?php
declare(strict_types=1);
session_start();
class User {
    private PDO $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function checkFieldsForUnique(string $login, string $email, string $phone): bool {
        $stmt = $this->pdo->prepare("SELECT login, email, phone FROM users 
                              WHERE login = :login OR email = :email OR phone = :phone");
        $stmt->execute([
            'login' => $login,
            'email' => $email,
            'phone' => $phone
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($user)) {
            return true;
        }

        if ($user['email'] == $email) {
            $_SESSION['validation_errors'][] = 'Пользователь с такой почтой уже зарегистрирован';
        }
        if ($user['login'] == $login) {
            $_SESSION['validation_errors'][] = 'Пользователь с таким логином уже зарегистрирован';
        }
        if ($user['phone'] == $phone) {
            $_SESSION['validation_errors'][] = 'Пользователь с таким номером телефона уже зарегистрирован';
        }

        return false;
    }

    public function checkFieldsForUniqueUpdate(string $login, string $email, string $phone): bool {
        $stmt = $this->pdo->prepare("SELECT login, email, phone FROM users 
                              WHERE (login = :login
                                 OR email = :email
                                 OR phone = :phone) AND id <> :id");
        $stmt->execute([
            'id' => $_SESSION['user']['id'],
            'login' => $login,
            'email' => $email,
            'phone' => $phone,
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (empty($user)) {
            return true;
        }

        if ($user['email'] == $email) {
            $_SESSION['validation_errors'][] = 'Пользователь с такой почтой уже зарегистрирован';
        }
        if ($user['login'] == $login) {
            $_SESSION['validation_errors'][] = 'Пользователь с таким логином уже зарегистрирован';
        }
        if ($user['phone'] == $phone) {
            $_SESSION['validation_errors'][] = 'Пользователь с таким номером телефона уже зарегистрирован';
        }

        return false;
    }

    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO users SET login = :login, email = :email,
                      phone = :phone, password = :password");

        return $stmt->execute([
            'login' => $data['login'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'password' => $data['password']
        ]);
    }

    public function getUser(string $login): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :login OR phone = :login");
        $stmt->execute([
            'login' => $login
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function changePassword(string $password, int $id): bool
    {
        $stmt = $this->pdo->prepare("UPDATE users SET password = :password WHERE id = :id");

        return $stmt->execute([
            'password' => $password,
            'id' => $id
        ]);
    }
}