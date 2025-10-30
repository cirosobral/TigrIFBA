<?php
// public/dashboard.php
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';
require_login();
$pdo = get_pdo();

// get user balance
$stmt = $pdo->prepare("SELECT id, email, display_name, balance FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// get games
$games = $pdo->query("SELECT id,title,provider_url FROM games ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>Dashboard - <?=htmlspecialchars($user['display_name'])?></title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
  <header>
    <div class="container">
      <h2>Plataforma Educativa de Jogos</h2>
      <nav>
        <span><?=htmlspecialchars($user['display_name'])?> (<?=htmlspecialchars($user['email'])?>)</span>
        <a href="/logout.php">Sair</a>
      </nav>
    </div>
  </header>
  <main class="container">
    <section class="card">
      <h3>Saldo</h3>
      <p class="big">R$ <?=number_format($user['balance'], 0, ',', '.')?></p>
      <button id="btn-refresh">Atualizar</button>
    </section>

    <section class="card">
      <h3>Jogos Disponíveis</h3>
      <ul>
        <?php foreach ($games as $g): ?>
        <li>
          <strong><?=htmlspecialchars($g['title'])?></strong>
          — <a href="/play.php?id=<?=intval($g['id'])?>" target="_self">Jogar</a>
        </li>
        <?php endforeach; ?>
      </ul>
    </section>

    <section class="card">
      <h3>Histórico (transações)</h3>
      <div id="tx-list">Carregando...</div>
    </section>
  </main>

  <script src="/assets/js/app.js"></script>
  <script>
  const userId = <?=json_encode($_SESSION['user_id'])?>;
  document.getElementById('btn-refresh').addEventListener('click', refresh);
  async function refresh() {
    const res = await fetch('/src/api.php?action=balance', {
      credentials: 'same-origin'
    });
    if (res.ok) {
      const j = await res.json();
      document.querySelector('.big').textContent = 'R$ ' + j.balance.toString();
    }
    const tx = await fetch('/src/api.php?action=transactions', {
      credentials: 'same-origin'
    });
    if (tx.ok) {
      const j = await tx.json();
      const el = document.getElementById('tx-list');
      if (j.length === 0) el.textContent = 'Sem transações';
      else {
        el.innerHTML = '<ul>' + j.map(t =>
          `<li>${t.created_at} — ${t.type} — ${t.amount} — ${t.description||''}</li>`).join('') + '</ul>';
      }
    }
  }
  refresh();
  </script>
</body>

</html>