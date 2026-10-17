<header>
    <a href="<?= BASE_URL ?>/" class="logo"></a>
    <nav>
        <ul>
            <li><a href="<?= BASE_URL ?>/">Strona główna</a></li>
        </ul>
    </nav>

    <?php if (!empty($_SESSION['userId'])) : ?>
    <form method="post" action="<?= BASE_URL ?>/logout">
        <button type="submit" class="login-btn btn-reset">Wyloguj</button>
    </form>
    <?php else : ?>
        <a href="<?= BASE_URL ?>/login" class="login-btn"></a>
    <?php endif; ?>
</header>