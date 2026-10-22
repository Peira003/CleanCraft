<?php
    $error = $_SESSION['form_error'] ?? null;
    unset($_SESSION['form_error']);
?>

<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/base.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/forms.css?>">
    <title>Document</title>
</head>
<body>
    <?php require __DIR__ . '/partials/header.php'; ?>



    <?php require __DIR__ . '/partials/footer.php'; ?>
</body>
</html>