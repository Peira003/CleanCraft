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

            <ul class="lista">
                <li class="uzytkownik">
                    <div class="awatar">AK</div>
                    <div class="dane">
                        <div class="imie">Anna</div>
                    </div>
                </li>

                <li class="uzytkownik">
                    <div class="awatar">JN</div>
                    <div class="dane">
                        <div class="imie">Jan</div>
                    </div>
                </li>

                <li class="uzytkownik">
                    <div class="awatar">MJ</div>
                    <div class="dane">
                        <div class="imie">Marek</div>
                    </div>
                </li>
            </ul>
        </section>
    </main>


    <?php require __DIR__ . '/partials/footer.php'; ?>

    <script src="<?= BASE_URL ?>/js/kalendarz.js"></script>
</body>
</html>
