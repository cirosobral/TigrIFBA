<?php
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/db.php';
require_admin();
$pdo = get_pdo();
$uCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$gCount = $pdo->query("SELECT COUNT(*) FROM games")->fetchColumn();
$tCount = $pdo->query("SELECT COUNT(*) FROM transactions")->fetchColumn();
?>
<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>Painel Admin</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
  <header class="container">
    <h2>Painel Administrativo</h2>
    <nav>
      <a href="/dashboard.php">Voltar ao usuário</a>
      <a href="/admin/users.php">Usuários</a>
      <a href="/admin/games.php">Jogos</a>
      <a href="/admin/transactions.php">Transações</a>
      <a href="/logout.php">Sair</a>
    </nav>
  </header>
  <main class="container">
    <div class="card">
      <h3>Resumo</h3>
      <p>Usuários: <?=$uCount?></p>
      <p>Jogos: <?=$gCount?></p>
      <p>Transações: <?=$tCount?></p>
    </div>
  </main>
</body>

</html>