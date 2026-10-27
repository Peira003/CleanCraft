<?php
/**
 * @var int $code
 * @var string $title
 * @var string $message
 */
?>

<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title) ?>></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/base.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/forms.css">
</head>
<body>

    <?php require __DIR__ . '/partials/header.php'; ?>

    <main>
        <div class="form-card wide error-card">
            <p class="error-code"><?= (int) $code ?></p>
            <h1><?= htmlspecialchars($title) ?></h1>
            <p class="chooser-info"><?= htmlspecialchars($message) ?></p>

            <div class="cta-buttons">
                <a href="<?= BASE_URL ?>/" class="btn-primary">Strona główna</a>
                <a href="javascript:history.back()" class="btn-secondary">Wróć</a>
            </div>
        </div>
    </main>

    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
