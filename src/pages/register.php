<div class="container">
    <h2>Форма регистрации</h2>
    <form action="../app/Controllers/AuthController.php" method="POST">
        <input required hidden type="text" name="action" value="register">
        <input required type="text" name="login" placeholder="Логин">
        <input required type="tel" name="phone" placeholder="Номер телефона" pattern="^8\d{10}$">
        <input required type="email" name="email" placeholder="Почта">
        <input required type="password" name="password" placeholder="Пароль">
        <input required type="password" name="password_confirmation" placeholder="Повторите пароль">
        <button type="submit">Регистрация</button>
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