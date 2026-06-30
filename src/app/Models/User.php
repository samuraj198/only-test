<?php
declare(strict_types=1);
session_start();
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function checkFieldsForUnique(string $name, string $email, string $phone): bool {
        $stmt = $this->pdo->prepare("SELECT name, email, phone FROM users 
                          WHERE name = :name OR email = :email OR phone = :phone");
        $stmt->execute([
            'name' => $name,
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
        if ($user['name'] == $name) {
            $_SESSION['validation_errors'][] = 'Пользователь с таким именем уже зарегистрирован';
        }
        if ($user['phone'] == $phone) {
            $_SESSION['validation_errors'][] = 'Пользователь с таким номером телефона уже зарегистрирован';
        }

        return false;
    }

    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO users SET name = :name, email = :email,
                      phone = :phone, password = :password");

        return $stmt->execute([
            'name' => $data['name'],
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
}