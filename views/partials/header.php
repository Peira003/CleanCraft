<?php
/**
 * @var bool|null $showBackButton
 */
$showBackButtonLogin = $showBackButtonLogin ?? false;
?>


<header>
    <a href="<?= BASE_URL ?>/" class="logo">CleanCraft</a>
    <nav>
        <ul>
            <li><a href="<?= BASE_URL ?>/">Strona główna</a></li>
            <?php if(!empty($_SESSION['userId'])): ?>
                <li><a href="<?= BASE_URL ?>/plans">Moje plany</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <?php if ($showBackButtonLogin): ?>
        <a href="<?= BASE_URL ?>/" class="back-btn">Wróć</a>
    <?php elseif (!empty($_SESSION['userId'])) : ?>
        <form method="post" action="<?= BASE_URL ?>/logout">
            <button type="submit" class="login-btn btn-reset">Wyloguj</button>
        </form>
    <?php else : ?>
        <a href="<?= BASE_URL ?>/login" class="login-btn">Zaloguj się</a>
    <?php endif; ?>
</header>