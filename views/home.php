<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title>CleanCraft</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/home.css">
</head>
<body>

<?php require __DIR__ . '/partials/header.php'; ?>


<main>
    <section class="hero-card">
        <h1>Organizuj sprzątanie bez konfliktów</h1>
        <p>
            <strong>CleanCraft</strong> to intuicyjna aplikacja do zarządzania grafikiem i kolejnością sprzątania mieszkania.
            Idealna dla mieszkań studenckich, współdzielonych oraz pod wynajem pokojowy.
        </p>

        <div class="cta-buttons">
            <a href="login" class="btn-primary">Rozpocznij teraz</a>
            <a href="#featueres" class="btn-secondary">Dowiedz się więcej</a>
        </div>
    </section>
</main>

<?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>