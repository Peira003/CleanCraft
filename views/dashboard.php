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
            <h2><?= htmlspecialchars($plan['name']) ?></h2>

            <?php if ($isOwner): ?>
                <div class="invite">
                    <div class="invite-info">
                        <p>Kod zaproszenia: <strong><?= htmlspecialchars($plan['invite_code']) ?></strong></p>
                        <p>Link: <?= htmlspecialchars($inviteLink) ?></p>
                    </div>


                    <form method="post" action="<?= BASE_URL ?>/plan/<?= (int) $plan['id'] ?>/code"
                          data-confirm="Wygenerować nowy kod? Stary kod i link przestaną działać."
                          data-confirm-label="Wygeneruj">
                        <?= \App\Csrf::field() ?>
                        <button type="submit" class="btn-secondary btn-small">Wygeneruj nowy kod</button>
                    </form>
                </div>
            <?php endif; ?>

            <ul class="lista">
                <?php foreach ($users as $u): ?>
                    <li class="uzytkownik">
                        <div class="awatar" style="background: <?= htmlspecialchars($u['color']) ?>; color: #0f1419">
                            <?= htmlspecialchars(mb_strtoupper(mb_substr($u['name'], 0, 1))) ?>
                        </div>
                        <div class="dane">
                            <div class="imie"><?= htmlspecialchars($u['name'])?></div>
                        </div>
                        <?php if($u['role'] === 'owner'): ?>
                            <span class="rola admin">Właściciel</span>
                        <?php endif; ?>

                        <?php if ($isOwner && $u['role'] !== 'owner'): ?>
                            <form method="post" action="<?= BASE_URL ?>/plan/<?= (int) $plan['id'] ?>/remove/<?= (int) $u['id'] ?>"
                                  data-confirm="Usunąć <?= htmlspecialchars($u['name'], ENT_QUOTES) ?> z planu?"
                                  data-confirm-label="Usuń">
                                <?= \App\Csrf::field() ?>
                                <button type="submit" class="remove-btn" aria-label="Usuń z planu">&times;</button>
                            </form>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <?php if (!$isOwner): ?>
                <form method="post"
                      action="<?= BASE_URL ?>/plan/<?= (int) $plan['id'] ?>/leave"
                      class="leave-form"
                      data-confirm="Na pewno chcesz opuścić ten plan?"
                      data-confirm-label="Opuść">
                    <?= \App\Csrf::field() ?>
                    <button type="submit" class="leave-btn">Opuść plan</button>
                </form>
            <?php endif; ?>
        </section>
    </main>


    <?php require __DIR__ . '/partials/footer.php'; ?>

    <dialog id="confirmModal" class="modal">
        <h3>Potwierdź</h3>
        <p id="modalText"></p>
        <div class="modal-actions">
            <button type="button" class="btn-secondary" id="modalCancel">Anuluj</button>
            <button type="button" class="btn-danger" id="modalConfirm">Potwierdź</button>
        </div>
    </dialog>

    <?php if ($isOwner): ?>
        <dialog id="weekModal" class="modal">
            <h3>Kto sprząta</h3>
            <p id="weekLabel"></p>
            <div class="picker" id="picker"></div>
            <p class="picker-error" id="weekError" hidden>Nie udało się zapisać. Odśwież stronę i spróbuj ponownie.</p>
            <div class="modal-actions">
                <button type="button" class="btn-secondary" id="weekCancel">Anuluj</button>
            </div>
        </dialog>
    <?php endif; ?>

    <script>
        window.PLAN = <?= json_encode([
                'isOwner' => $isOwner,
                'csrf' => \App\Csrf::token(),
            'url' => BASE_URL . '/plan/' . (int) $plan['id'] . '/schedule',
            'members' => array_map(fn($m) => [
                    'id' => (int) $m['id'], 'name' => $m['name'], 'color' => $m['color'],
            ], $users),
            'schedule' => (object) $schedule,
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ?>;
    </script>
    <script src="<?= BASE_URL ?>/js/modal.js"></script>
    <script src="<?= BASE_URL ?>/js/kalendarz.js"></script>
</body>
</html>
