<?php
require_once __DIR__ . "/../../db/mysql_connection.php";
require_once __DIR__ . "/../Models/User.php";

class UserService {
    private $pdo;
    private $userModel;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
    }

    public function register(array $data): ?bool
    {
        $validatedData = $this->validate($data);

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

        return null;
    }

    protected function validate(array $data): ?array
    {
        if ($data['password'] != $data['password_confirmation']) {
            $_SESSION['validation_errors'][] = "Пароли не совпадают";
            return null;
        }
        $check = $this->userModel->checkFieldsForUnique($data['name'], $data['email'], $data['phone']);

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
                unset($user['password']);
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

    public function logout()
    {
        if (isset($_SESSION['user'])) {
            unset($_SESSION['user']);
            $_SESSION['validation_errors'] = [];
        }

        return true;
    }
}