<?php
require_once __DIR__ . '/../../src/auth.php';
require_once __DIR__ . '/../../src/db.php';
require_admin();
$pdo = get_pdo();

// Ações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $url = trim($_POST['url']);
    if ($title && $url) {
        if (isset($_POST['id']) && $_POST['id']) {
            $pdo->prepare("UPDATE games SET title=?, provider_url=? WHERE id=?")
                ->execute([$title, $url, $_POST['id']]);
        } else {
            $pdo->prepare("INSERT INTO games (title, provider_url) VALUES (?,?)")
                ->execute([$title, $url]);
        }
        header('Location: games.php');
        exit;
    }
}

if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM games WHERE id=?")->execute([$_GET['delete']]);
    header('Location: games.php');
    exit;
}

$editGame = null;
if (isset($_GET['edit'])) {
    $stmt = $pdo->prepare("SELECT * FROM games WHERE id=?");
    $stmt->execute([$_GET['edit']]);
    $editGame = $stmt->fetch(PDO::FETCH_ASSOC);
}

$games = $pdo->query("SELECT * FROM games ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>Admin - Jogos</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
  <header class="container">
    <h2>Gerenciar Jogos</h2>
    <nav>
      <a href="/admin/">Início</a> |
      <a href="/admin/users.php">Usuários</a> |
      <a href="/admin/transactions.php">Transações</a>
    </nav>
  </header>
  <main class="container">
    <div class="card">
      <h3><?= $editGame ? 'Editar Jogo' : 'Adicionar Novo Jogo' ?></h3>
      <form method="post">
        <input type="hidden" name="id" value="<?=htmlspecialchars($editGame['id'] ?? '')?>">
        <label>Título:<input type="text" name="title" required
            value="<?=htmlspecialchars($editGame['title'] ?? '')?>"></label>
        <label>URL do provedor:<input type="url" name="url" required
            value="<?=htmlspecialchars($editGame['provider_url'] ?? '')?>"></label>
        <button type="submit">Salvar</button>
      </form>
    </div>

    <div class="card">
      <h3>Lista de Jogos</h3>
      <table border="1" cellpadding="6">
        <tr>
          <th>ID</th>
          <th>Título</th>
          <th>URL</th>
          <th>Ações</th>
        </tr>
        <?php foreach ($games as $g): ?>
        <tr>
          <td><?=$g['id']?></td>
          <td><?=htmlspecialchars($g['title'])?></td>
          <td><a href="<?=$g['provider_url']?>" target="_blank">Abrir</a></td>
          <td><a href="?edit=<?=$g['id']?>">Editar</a> |
            <a href="?delete=<?=$g['id']?>" onclick="return confirm('Excluir jogo?')">Excluir</a>
          </td>
        </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </main>
</body>

</html>