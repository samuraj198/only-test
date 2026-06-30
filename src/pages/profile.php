<?php
if (empty($_SESSION['user'])) {
    header('Location: /');
}
?>
<div class="container">
    <h2>Профиль</h2>
    <form action="" method="POST">
        <h3>Изменение данных профиля</h3>
        <input type="text" name="action" value="changeData" hidden>
        <label>
            Новое имя
            <input required type="text" name="name" placeholder="Новое имя"
                   value="<?=$_SESSION['user']['name']?>">
        </label>
        <label>
            Новая почта
            <input required type="email" name="name" placeholder="Новая почта"
                   value="<?=$_SESSION['user']['email']?>">
        </label>
        <label>
            Новый номер телефона
            <input required type="tel" name="name" placeholder="Новый номер телефона"
                   value="<?=$_SESSION['user']['phone']?>">
        </label>
        <label>
            Подтверждение паролем
            <input required type="password" name="name" placeholder="Пароль">
        </label>
        <button type="submit">Изменить</button>
    </form>

    <form action="" method="POST">
        <h3>Изменение пароля</h3>
        <input type="text" name="action" value="changePassword" hidden>
        <label>
            Старый пароль
            <input type="text" name="name" placeholder="Старый пароль">
        </label>
        <label>
            Новый пароль
            <input type="text" name="name" placeholder="Новый пароль">
        </label>
        <label>
            Повторите пароль
            <input type="text" name="name" placeholder="Повторите пароль">
        </label>
        <button type="submit">Изменить</button>
    </form>
</div>