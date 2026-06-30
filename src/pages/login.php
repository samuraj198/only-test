<div class="container">
    <h2>Форма авторизации</h2>
    <form action="../app/Controllers/AuthController.php" method="POST">
        <input hidden type="text" name="action" value="login">
        <input required type="text" name="login" placeholder="Почта или номер телефона">
        <input required type="password" name="password" placeholder="Пароль">
        <div    style="max-width: 200px;"
                id="captcha-container"
                class="smart-captcha"
                data-sitekey="<?=parse_ini_file(__DIR__ . '/../.env')['YANDEX_CLIENT_KEY']?>"
        ></div>
        <button type="submit">Вход</button>
    </form>
    <div class="errors">
        <?php
        if (isset($_SESSION['validation_errors'])) {
            foreach ($_SESSION['validation_errors'] as $error) { ?>
                <p class="error" style="color: red">
                    <?=$error?>
                </p>
            <?php }
        }
        unset($_SESSION['validation_errors']);
        ?>
    </div>
</div>
<script src="https://smartcaptcha.cloud.yandex.ru/captcha.js" defer></script>