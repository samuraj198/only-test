<?php
require_once __DIR__ . "/../../db/mysql_connection.php";
require_once __DIR__ . "/../Models/User.php";

class UserService {
    private PDO $pdo;
    private User $userModel;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
    }

    public function register(array $data): bool
    {
        $validatedData = $this->validateForRegister($data);

        if ($validatedData != null) {
            $validatedData['password'] = password_hash($validatedData['password'], PASSWORD_DEFAULT);
            $created = $this->userModel->create($validatedData);

            if ($created) {
                $_SESSION['user'] = $this->userModel->getUser($data['email']);

                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    protected function validateForRegister(array $data): ?array
    {
        if ($data['password'] != $data['password_confirmation']) {
            $_SESSION['validation_errors'][] = "Пароли не совпадают";
            return null;
        }
        if (strlen($data['password']) < 6) {
            $_SESSION['validation_errors'][] = 'Пароль должен быть минимум из 6 символов';
            return null;
        }
        if (strlen($data['login']) < 2) {
            $_SESSION['validation_errors'][] = 'Логин должен быть минимум из 2 символов';
            return null;
        }
        $check = $this->userModel->checkFieldsForUnique($data['login'], $data['email'], $data['phone']);

        if ($check) {
            $_SESSION['validation_errors'] = [];
            return $data;
        }

        return null;
    }

    public function login(array $data): bool
    {
        if (empty($data['smart-token'])) {
            $_SESSION['validation_errors'][] = 'Вы не прошли капчу';
            return false;
        }

        $checkCaptcha = $this->checkCaptchaToken($data['smart-token']);

        if (!$checkCaptcha) {
            $_SESSION['validation_errors'][] = 'Вы не смогли пройти капчу';
            return false;
        }

        $user = $this->userModel->getUser($data['login']);

        if (empty($user)) {
            $_SESSION['validation_errors'][] = "Пользователя с такой почтой не существует";

            return false;
        } else {
            if (password_verify($data['password'], $user['password'])) {
                $_SESSION['user'] = $user;
                $_SESSION['validation_errors'] = [];

                return true;
            } else {
                $_SESSION['validation_errors'][] = "Неверный пароль";

                return false;
            }
        }
    }

    protected function checkCaptchaToken(string $token): bool
    {
        $ch = curl_init("https://smartcaptcha.cloud.yandex.ru/validate");
        $args = [
            "secret" => parse_ini_file(__DIR__ . '/../../.env')['YANDEX_SERVER_KEY'],
            "token" => $token,
            "ip" => $_SERVER['REMOTE_ADDR']
        ];
        curl_setopt($ch, CURLOPT_TIMEOUT, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($args));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode !== 200) {
            echo "Allow access due to an error: code=$httpcode; message=$server_output\n";
            return true;
        }

        $resp = json_decode($server_output);
        return $resp->status === "ok";
    }

    public function logout(): bool
    {
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
            $_SESSION['validation_errors'] = [];

            return true;
        }

        return false;
    }

    public function update(array $data): bool
    {
        $validatedData = $this->validateForUpdate($data);

        if ($validatedData != null) {
            if ($validatedData['login'] == $_SESSION['user']['login']
            && $validatedData['email'] == $_SESSION['user']['email']
            && $validatedData['phone'] == $_SESSION['user']['phone']) {
                $_SESSION['validation_errors'][] = 'Изменили данные хотя бы в одном поле';
                return false;
            }
            if (strlen($data['login']) < 2) {
                $_SESSION['validation_errors'][] = 'Логин должен быть минимум из 2 символов';
                return false;
            }

            $stmt = $this->pdo->prepare("UPDATE `users` SET login = :login, 
                                                           email = :email, 
                                                           phone = :phone 
                                                       WHERE id = :id");
            $check = $stmt->execute([
                'login' => $data['login'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'id' => $_SESSION['user']['id'],
            ]);

            if ($check) {
                $_SESSION['user'] = $this->userModel->getUser($data['email']);
                $_SESSION['success'] = 'Вы успешно изменили данные своего профиля';

                return true;
            }

            return false;
        }

        return false;
    }

    protected function validateForUpdate(array $data): ?array
    {
        if (!password_verify($data['password'], $_SESSION['user']['password'])) {
            $_SESSION['validation_errors'][] = 'Неверный пароль';
            return null;
        }

        if (empty($data['login'])) {
            $data['login'] = $_SESSION['user']['login'];
        }
        if (empty($data['email'])) {
            $data['email'] = $_SESSION['user']['email'];
        }
        if (empty($data['phone'])) {
            $data['phone'] = $_SESSION['user']['phone'];
        }

        $check = $this->userModel->checkFieldsForUniqueUpdate($data['login'], $data['email'], $data['phone']);

        if ($check) {
            $_SESSION['validation_errors'] = [];
            return $data;
        }

        return null;
    }

    public function changePassword(array $data): bool
    {
        if (!password_verify($data['old_password'], $_SESSION['user']['password'])) {
            $_SESSION['validation_errors'][] = 'Неверный пароль';
            return false;
        }

        if (password_verify($data['new_password'], $_SESSION['user']['password'])) {
            $_SESSION['validation_errors'][] = 'Новый пароль должен отличаться от старого';
            return false;
        }

        if (strlen($data['new_password']) < 6) {
            $_SESSION['validation_errors'][] = 'Новый пароль должен быть минимум из 6 символов';
            return false;
        }

        if ($data['new_password'] != $data['new_password_confirmation']) {
            $_SESSION['validation_errors'][] = 'Пароли не совпадают';
            return false;
        }

        $changePasswordCheck = $this->userModel->
            changePassword(
                password_hash(
                    $data['new_password'],
                    PASSWORD_DEFAULT),
                $_SESSION['user']['id']
            );

        if ($changePasswordCheck) {
            $_SESSION['user'] = $this->userModel->getUser($_SESSION['user']['email']);
            $_SESSION['success'] = 'Вы успешно изменили свой пароль';
            return true;
        }

        return false;
    }
}