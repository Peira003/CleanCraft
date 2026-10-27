<?php
$error = $_SESSION['form_error'] ?? null;
unset($_SESSION['form_error']);
?>

<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Utwórz plan</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/base.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/forms.css">
</head>
<body>
    <?php require __DIR__ . '/../partials/header.php' ?>

    <main>
        <div class="form-card">
            <h1>Utwórz plan</h1>

            <?php if ($error) : ?>
                <p class="form-error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="post" action="<?= BASE_URL ?>/plans/create">
                <?= \App\Csrf::field() ?>
                <label for="name">Nazwa planu</label>
                <input type="text" id="name" name="name" maxlength="120" placeholder="np. Mieszkanie na Kwiatowej" required>
                <input type="submit" value="Utwórz">
            </form>

            <p class="form-link"><a href="<?= BASE_URL ?>/plans">Wróć</a></p>
        </div>
    </main>

    <?php require __DIR__ . '/../partials/footer.php' ?>
</body>
</html>
