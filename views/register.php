<?php
    $error = $_SESSION['form_error'] ?? null;
    unset($_SESSION['form_error']);
?>

<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CleanCraft - Rejestracja</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/base.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/forms.css?>">
</head>
<body>
    <?php
        $showBackButton = true;
        require __DIR__ . '/partials/header.php';
    ?>

    <main>
        <div class="form-card">
            <h1>Załóż konto</h1>

            <?php if ($error) : ?>
                <p class="form-error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="post" action="<?= BASE_URL ?>/register">
                <?= \App\Csrf::field() ?>
                <label for="name">Imię</label>
                <input type="text" id="name" name="name" maxlength="30" required>

                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Hasło (min. 8 znaków)</label>
                <input type="password" id="password" name="password" minlength="8" maxlength="72" required>

                <label for="password_confirm">Powtórz hasło</label>
                <input type="password" id="password_confirm" name="password_confirm" minlength="8" maxlength="72" required>

                <input type="submit" value="Zarejestruj się">
            </form>

            <p class="form-link">Masz już konto? <a href="<?= BASE_URL ?>/login">Zaloguj się</a></p>
        </div>
    </main>


    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>