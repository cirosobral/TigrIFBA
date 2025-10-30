<?php
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/db.php';
require_admin();
$pdo = get_pdo();
$tx = $pdo->query("SELECT t.id,u.email,t.amount,t.type,t.description,t.created_at 
                   FROM transactions t 
                   JOIN users u ON u.id=t.user_id 
                   ORDER BY t.id DESC LIMIT 100")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>Admin - Transações</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
  <header class="container">
    <h2>Transações Recentes</h2>
    <nav>
      <a href="/admin/">Início</a> |
      <a href="/admin/users.php">Usuários</a> |
      <a href="/admin/games.php">Jogos</a>
    </nav>
  </header>
  <main class="container">
    <table border="1" cellpadding="6">
      <tr>
        <th>ID</th>
        <th>Email</th>
        <th>Tipo</th>
        <th>Valor</th>
        <th>Descrição</th>
        <th>Data</th>
      </tr>
      <?php foreach ($tx as $t): ?>
      <tr>
        <td><?=$t['id']?></td>
        <td><?=htmlspecialchars($t['email'])?></td>
        <td><?=htmlspecialchars($t['type'])?></td>
        <td><?=$t['amount']?></td>
        <td><?=htmlspecialchars($t['description'])?></td>
        <td><?=$t['created_at']?></td>
      </tr>
      <?php endforeach; ?>
    </table>
  </main>
</body>

</html>