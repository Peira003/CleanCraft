<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/dashboard.css">
    <title>Panel</title>
</head>
<body>
    <header>
        <a href="<?= BASE_URL ?>/" class="logo">CleanCraft</a>
        <nav>
            <ul>
                <li class="page"><a href="<?= BASE_URL ?>/">Strona główna</a></li>
            </ul>
        </nav>
        <a href="<?= BASE_URL ?>/" class="back-btn">Wróć</a>
    </header>

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
            <h2>Użytkownicy</h2>

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
                    <div class="awatar">MW</div>
                    <div class="dane">
                        <div class="imie">Martyna</div>
                    </div>
                </li>
            </ul>
        </section>
    </main>


    <footer>
        <p>&copy; 2026 Stworzone przez Peira. Wszystkie prawa zastrzeżone.</p>
    </footer>
<!---->
<!--    <script>-->
<!--        const wydarzenia = --><?php //= json_encode($wydarzenia,JSON_UNESCAPED_UNICODE) ?>
<!--   </script>-->
    <script src="<?= BASE_URL ?>/js/kalendarz.js"></script>
</body>
</html>
