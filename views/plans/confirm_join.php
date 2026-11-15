<?php
/**
 * @var array $plan
 * @var string $inviteCode
 */
?>

<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dołącz do planu</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/base.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/forms.css">
</head>
<body>
    <?php require __DIR__ . '/../partials/header.php'; ?>

    <main>
        <div class="form-card wide">
            <h1>Zaproszenie do planu</h1>
            <p class="chooser-info">
                Zostałeś zaproszonu do planu
                <strong><?= htmlspecialchars($plan['name']) ?></strong>.
                Chcesz do niego dołączyć?
            </p>

            <form method="post" class="plain-form" action="<?= BASE_URL ?>/dolacz/<?= htmlspecialchars(rawurlencode($inviteCode)) ?>">
                <?= \App\Csrf::field() ?>
                <div class="cta-buttons">
                    <button type="submit" class="btn-primary btn-reset">Dołącz do planu</button>
                    <a href="<?= BASE_URL ?>/plans" class="btn-secondary">Anuluj</a>
                </div>
            </form>
        </div>
    </main>

    <?php require __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>