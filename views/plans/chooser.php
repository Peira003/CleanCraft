<?php

/**
 * @var array $plany
 */
?>

<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Twoje plany</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/base.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/forms.css">
</head>
<body>
    <?php require __DIR__ . '/partials/header.php'; ?>

    <main>
        <div class="form-card wide"></div>
            <?php if(empty($plany)): ?>
                <h1>Witaj w CleanCraft!</h1>
                <p class="chooser-info">
                    Nie należysz jeszcze do żadnego planu. Utwórz własny albo dołącz do istniejącego kodem lub linkiem.
                </p>
            <?php else: ?>
                <h1>Twoje plany</h1>
                <ul class="plan-list">
                    <?php foreach($plany as $plan): ?>
                        <li>
                            <a href="<?= BASE_URL ?>/plany/<?= (int) $plan['id'] ?>">
                                <span><?= htmlspecialchars($plan['name']) ?></span>
                                <span class="badge" <?= $plan['role'] === 'owner' ? 'owner' : '' ?> >
                                    <?= $plan['role'] === 'owner' ? 'Właściciel' : 'Członek' ?>
                                </span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <div class="cta-buttons">
                <a href="<?= BASE_URL ?>/plans/create" class="btn-primary">Utwórz plan</a>
                <a href="<?= BASE_URL ?>/plans/join" class="btn-secondary">Dołącz do planu</a>
            </div>
    </main>

    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
