<div class="container">
    <h2>Главная страница</h2>
    <?php if (isset($_SESSION['auth_error'])) { ?>
        <p style="color: red"><?=$_SESSION['auth_error'] ?></p>
    <?php
    }
    unset($_SESSION['auth_error']);
    ?>
</div>