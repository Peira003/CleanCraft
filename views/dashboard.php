<?php
/**
 * @var array $plan
 * @var array $users
 * @var bool $isOwner
 */



$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$inviteLink = $scheme . '://' . $_SERVER['HTTP_HOST'] . BASE_URL . '/dolacz/' . $plan['invite_code'];
?>


<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($plan['name']) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/base.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/dashboard.css">
</head>
<body>
    <?php require __DIR__ . '/partials/header.php'; ?>

    <main class="dashboardLayout">
        <section class="panelCard">
            <div class="kalendarzNaglowek">
                <button type="button" class="strzalka" id="poprzedni" aria-label="Poprzedni miesiąc">&#8249;</button>
                <h2 id="tytul"></h2>
                <button type="button" class="strzalka" id="nastepny" aria-label="Następny miesiąc">&#8250;</button>
            </div>
            <div class="siatka" id="siatka"></div>
        </section>

        <section class="panelCard">
            <h2><?php htmlspecialchars($plan['name']) ?></h2>

            <?php if ($isOwner): ?>
                <div class="invite">
                    <p>Kod zaproszenia: <strong><?= htmlspecialchars($plan['invite_code']) ?></strong></p>
                    <p>Link: <?= htmlspecialchars($inviteLink) ?></p>
                </div>
            <?php endif; ?>

            <ul class="lsita">
                <?php foreach ($users as $u): ?>
                    <li class="uzytkownicy">
                        <div class="awater"><?= htmlspecialchars(mb_strtoupper(mb_substr($u['name'], 0, 1))) ?></div>
                        <div class="dane">
                            <div class="imie"><?= htmlspecialchars($u['name'])?></div>
                        </div>
                        <?php if($u['role'] === 'owner'): ?>
                            <span class="rola-admina">Właściciel</span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    </main>


    <?php require __DIR__ . '/partials/footer.php'; ?>

    <script src="<?= BASE_URL ?>/js/kalendarz.js"></script>
</body>
</html>
