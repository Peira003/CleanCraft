<?php

require __DIR__ . '/vendor/autoload.php';

use Config\Database;

$pdo = Database::getConnection();

$pdo->exec('CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');

$created = $pdo->query("SELECT name FROM migrations")->fetchAll(PDO::FETCH_COLUMN);

$files = glob(__DIR__ . '/migrations/*.sql');
sort($files);

$cel = $argv[1] ?? null;

if ($cel !== null) {
    $files = array_filter($files, fn($f) => str_starts_with(basename($f), $cel));

    if (!$files) {
        echo "Nie znaleziono migracji: $cel\n";
        exit(1);
    }
}

foreach ($files as $file) {
    $name = basename($file);
    if (in_array($name, $created, true)) {
        continue;
    }

    echo "Uruchamiam : $name\n";
    $pdo->exec(file_get_contents($file));

    $pdo->prepare('INSERT INTO migrations (name) VALUES (:name)')
        ->execute(['name' => $name]);
}

echo "Gotowe.\n";