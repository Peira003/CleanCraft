<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CleanCraft - Strona główna</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/login.css">
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

    <main>
        <div id="container"> 
            <h1>Login</h1>
            <form action="/src/Controllers/AuthController.php" method="post">
                <label for="email">Email:</label></br>
                <input type="email" id="email" name="email" required></br></br>
                <label for="password">Password:</label></br>
                <input type="password" id="password" name="password" required></br></br>
                <input type="submit" value="Login">
                <a href="#" class="register-btn">Zarejestruj się</a>
            </form>
        </div> 
    </main>

    <footer>
        <p>&copy; 2026 Stworzone przez Peira. Wszystkie prawa zastrzeżone.</p>
    </footer>
</body>
</html>