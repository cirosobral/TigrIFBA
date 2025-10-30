<?php
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/db.php';
require_admin();
$pdo = get_pdo();

// Ações
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM users WHERE id=? AND is_admin=0")->execute([$_GET['delete']]);
    header('Location: users.php');
    exit;
}
if (isset($_GET['reset'])) {
    $pdo->prepare("UPDATE users SET balance=1000 WHERE id=?")->execute([$_GET['reset']]);
    header('Location: users.php');
    exit;
}

$users = $pdo->query("SELECT id,email,display_name,balance,is_admin FROM users ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>Admin - Usuários</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
  <header class="container">
    <h2>Gerenciar Usuários</h2>
    <nav>
      <a href="/admin/">Início</a> |
      <a href="/admin/games.php">Jogos</a> |
      <a href="/admin/transactions.php">Transações</a>
    </nav>
  </header>
  <main class="container">
    <table border="1" cellpadding="6">
      <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Email</th>
        <th>Saldo</th>
        <th>Admin?</th>
        <th>Ações</th>
      </tr>
      <?php foreach ($users as $u): ?>
      <tr>
        <td><?=$u['id']?></td>
        <td><?=htmlspecialchars($u['display_name'])?></td>
        <td><?=htmlspecialchars($u['email'])?></td>
        <td><?=$u['balance']?></td>
        <td><?=$u['is_admin'] ? 'Sim' : 'Não'?></td>
        <td>
          <?php if (!$u['is_admin']): ?>
          <a href="?reset=<?=$u['id']?>">Resetar saldo</a> |
          <a href="?delete=<?=$u['id']?>" onclick="return confirm('Excluir este usuário?')">Excluir</a>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </table>
  </main>
</body>

</html>