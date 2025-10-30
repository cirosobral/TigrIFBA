<?php
// data/init_db.php
require_once __DIR__ . '/../src/db.php';
$pdo = get_pdo();

// Create tables
$pdo->exec("CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  email TEXT UNIQUE NOT NULL,
  password TEXT NOT NULL,
  display_name TEXT,
  balance INTEGER NOT NULL DEFAULT 1000,
  is_admin INTEGER NOT NULL DEFAULT 0,
  created_at TEXT DEFAULT CURRENT_TIMESTAMP
)");


$pdo->exec("CREATE TABLE IF NOT EXISTS games (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    provider_url TEXT NOT NULL,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("CREATE TABLE IF NOT EXISTS transactions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    amount INTEGER NOT NULL,
    type TEXT NOT NULL,
    description TEXT,
    created_at TEXT DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY(user_id) REFERENCES users(id)
)");

// Seed default user (if not exists)
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
$count = (int)$stmt->execute() && (int)$stmt->fetchColumn();
$stmt = $pdo->query("SELECT COUNT(*) FROM users");
if ((int)$stmt->fetchColumn() === 0) {
  $password = password_hash('senha123', PASSWORD_DEFAULT);
  $pdo->prepare("INSERT INTO users (email,password,display_name,balance,is_admin) VALUES (?,?,?,?,1)")
    ->execute(['admin@example.com', $password, 'Admin', 10000]);
  echo "Usuário seed: admin@example.com / senha123\n";
}

// Seed games (if none)
$stmt = $pdo->query("SELECT COUNT(*) FROM games");
if ((int)$stmt->fetchColumn() === 0) {
  $games = [
    ['title' => 'Jogo Exemplo 1', 'provider_url' => 'https://example.com/game1'],
    ['title' => 'Jogo Exemplo 2', 'provider_url' => 'https://example.com/game2'],
    ['title' => 'Jogo Exemplo 3', 'provider_url' => 'https://www.example.org/mini-game']
  ];
  $ins = $pdo->prepare("INSERT INTO games (title, provider_url) VALUES (?,?)");
  foreach ($games as $g) $ins->execute([$g['title'], $g['provider_url']]);
  echo "Seed games criados.\n";
}

echo "Init complete. DB: " . get_db_path() . "\n";
