<?php
if (empty($_SESSION['user'])) {
    header('Location: /');
}
?>
<div class="container">
    <h2>Профиль</h2>
    <div class="form-1">
        <form action="../app/Controllers/AuthController.php" method="POST">
            <h3>Изменение данных профиля</h3>
            <input type="text" name="action" value="updateData" hidden>
            <label>
                Новое имя
                <input type="text" name="name" placeholder="Новое имя"
                       value="<?=$_SESSION['user']['name']?>">
            </label>
            <label>
                Новая почта
                <input type="email" name="email" placeholder="Новая почта"
                       value="<?=$_SESSION['user']['email']?>">
            </label>
            <label>
                Новый номер телефона
                <input type="tel" name="phone" placeholder="Новый номер телефона"
                       pattern="^8\d{10}$" value="<?=$_SESSION['user']['phone']?>">
            </label>
            <label>
                Подтверждение паролем
                <input required type="password" name="password" placeholder="Пароль">
            </label>
            <button type="submit">Изменить</button>
        </form>
    </div>

    <div class="form-2">
        <form action="../app/Controllers/AuthController.php" method="POST">
            <h3>Изменение пароля</h3>
            <input type="text" name="action" value="changePassword" hidden>
            <label>
                Старый пароль
                <input type="password" name="old_password" placeholder="Старый пароль">
            </label>
            <label>
                Новый пароль
                <input type="password" name="new_password" placeholder="Новый пароль">
            </label>
            <label>
                Повторите пароль
                <input type="password" name="new_password_confirmation" placeholder="Повторите пароль">
            </label>
            <button type="submit">Изменить</button>
        </form>
    </div>
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