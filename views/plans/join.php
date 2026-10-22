<?php
$error = $_SESSION['form_error'] ?? null;
unset($_SESSION['form_error']);
?>

<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dołącz do planu</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/base.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/form.css">
</head>
<body>
    <?php require __DIR__ . '/../partials/header.php' ?>

    <main>
        <div class="form-card">
            <h1>Dołącz do planu</h1>

            <?php if ($error) : ?>
                <p class="form-error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="post" action="<?= BASE_URL ?>/plans/join">
                <label for="code">Kod zaproszenia</label>
                <input type="text" id="code" name="code" maxlength="120" placeholder="np. K7P3QX" autocomplete="off" required>
                <input type="submit" value="Dołącz">
            </form>

            <p class="form-link"><a href="<?= BASE_URL ?>/plans">Wróć</a></p>
        </div>
    </main>

    <?php require __DIR__ . '/../partials/footer.php' ?>
</body>
</html>