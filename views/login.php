<?php
    $error = $_SESSION['form_error'] ?? null;
    unset($_SESSION['form_error']);
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
    <?php
        $showBackButtonLogin = true;
        require __DIR__ . '/partials/header.php';
    ?>

    <main>
        <div class="form-card">
            <h1>Logowanie</h1>

            <?php if ($error): ?>
                <p class="form-error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="post" action="<?= BASE_URL ?>/login">
                <?= \App\Csrf::field() ?>
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