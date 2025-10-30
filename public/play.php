<?php
// public/play.php
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';
require_login();

$id = intval($_GET['id'] ?? 0);
$pdo = get_pdo();
$stmt = $pdo->prepare("SELECT id,title,provider_url FROM games WHERE id = ?");
$stmt->execute([$id]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$game) {
    http_response_code(404);
    echo "Jogo não encontrado.";
    exit;
}
?>
<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>Jogar — <?=htmlspecialchars($game['title'])?></title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
  <header class="container">
    <a href="/dashboard.php">&larr; Voltar</a>
    <h2><?=htmlspecialchars($game['title'])?></h2>
  </header>
  <main class="container">
    <div class="card">
      <p>O jogo é servido por um provedor externo e será exibido abaixo (iframe). Como medida de segurança, se o
        provedor bloquear framing, será aberto em nova aba.</p>

      <div class="iframe-wrap">
        <iframe src="<?=htmlspecialchars($game['provider_url'])?>" title="<?=htmlspecialchars($game['title'])?>"
          sandbox="allow-scripts allow-forms allow-same-origin" width="100%" height="600"></iframe>
      </div>

      <p>Nota: os jogos usam moeda fictícia. Use os endpoints REST para integração.</p>
    </div>
  </main>
</body>

</html>