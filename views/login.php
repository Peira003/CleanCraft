<?php
    $badAttempt = !empty($_SESSION['bad_attempt']);
    unset($_SESSION['bad_attempt']);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CleanCraft - Logowanie</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/base.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/forms.css">
</head>
<body>
    <?php require __DIR__ . '/partials/header.php'; ?>

    <main>
        <div class="form-card">
            <h1>Logowanie</h1>

            <?php if ($badAttempt): ?>
                <p class="form-error">Nieprawidłowy e-mail lub hasło.</p>
            <?php endif; ?>

            <form method="post" action="<?= BASE_URL ?>/login">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required>

                <label for="password">Hasło</label>
                <input type="password" id="password" name="password" required>

                <input type="submit" value="Zaloguj się">
            </form>

            <p class="form-link">Nie masz konta? <a href="<?= BASE_URL ?>/register">Zarejestruj się</a></p>
        </div>
    </main>

    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>