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
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TigrIFBA - Dashboard - <?= htmlspecialchars($user['display_name']) ?></title>

  <!-- Bootstrap core & SB Admin 2 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">

  <!-- Custom styles -->
  <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body id="page-top">

  <!-- Page Wrapper -->
  <div id="wrapper">

    <!-- Sidebar -->
    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

      <!-- Sidebar - Brand -->
      <a class="sidebar-brand d-flex align-items-center justify-content-center" href="/dashboard.php">
        <div class="sidebar-brand-icon rotate-n-15">
          <i class="fas fa-gamepad"></i>
        </div>
        <div class="sidebar-brand-text mx-3">TigrIFBA</div>
      </a>

      <hr class="sidebar-divider my-0">

      <li class="nav-item active">
        <a class="nav-link" href="/dashboard.php">
          <i class="fas fa-fw fa-tachometer-alt"></i>
          <span>Dashboard</span></a>
      </li>

      <hr class="sidebar-divider">

      <li class="nav-item">
        <a class="nav-link" href="/logout.php">
          <i class="fas fa-sign-out-alt"></i>
          <span>Sair</span>
        </a>
      </li>

      <hr class="sidebar-divider d-none d-md-block">
    </ul>
    <!-- End of Sidebar -->


    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

      <!-- Main Content -->
      <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item dropdown no-arrow">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <span class="me-2 d-none d-lg-inline text-gray-600 small">
                  <?= htmlspecialchars($user['display_name']) ?> (<?= htmlspecialchars($user['email']) ?>)
                </span>
                <img class="img-profile rounded-circle"
                  src="https://ui-avatars.com/api/?name=<?= urlencode($user['display_name']) ?>&background=4e73df&color=fff"
                  width="32" height="32">
              </a>
              <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="/logout.php">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                  Sair
                </a>
              </div>
            </li>
          </ul>
        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

          <!-- Saldo -->
          <div class="row">
            <div class="col-xl-4 col-md-6 mb-4">
              <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                  <div class="row no-gutters align-items-center">
                    <div class="col me-2">
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Saldo Atual</div>
                      <div class="h5 mb-0 font-weight-bold text-gray-800 big">R$
                        <?= number_format($user['balance'], 0, ',', '.') ?></div>
                    </div>
                    <div class="col-auto">
                      <i class="fas fa-wallet fa-2x text-gray-300"></i>
                    </div>
                  </div>
                  <button id="btn-refresh" class="btn btn-sm btn-outline-primary mt-3">
                    <i class="fas fa-sync-alt"></i> Atualizar
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Jogos disponíveis -->
          <div class="row">
            <div class="col-lg-12 mb-4">
              <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                  <h6 class="m-0 font-weight-bold text-primary">Jogos Disponíveis</h6>
                </div>
                <div class="card-body">
                  <div class="list-group">
                    <?php foreach ($games as $g): ?>
                      <a href="/play.php?id=<?= intval($g['id']) ?>"
                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <span><i
                            class="fas fa-puzzle-piece text-secondary me-2"></i><?= htmlspecialchars($g['title']) ?></span>
                        <span class="badge bg-primary">Jogar</span>
                      </a>
                    <?php endforeach; ?>
                    <?php if (empty($games)): ?>
                      <div class="text-muted">Nenhum jogo disponível no momento.</div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Histórico de transações -->
          <div class="row">
            <div class="col-lg-12 mb-4">
              <div class="card shadow mb-4">
                <div class="card-header py-3">
                  <h6 class="m-0 font-weight-bold text-primary">Histórico de Transações</h6>
                </div>
                <div class="card-body">
                  <div id="tx-list" class="small text-gray-700">Carregando...</div>
                </div>
              </div>
            </div>
          </div>

        </div>
        <!-- /.container-fluid -->

      </div>
      <!-- End of Main Content -->

    </div>
    <!-- End of Content Wrapper -->

  </div>
  <!-- End of Page Wrapper -->

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/js/sb-admin-2.min.js"></script>

  <script src="/assets/js/app.js"></script>
  <script>
    const userId = <?= json_encode($_SESSION['user_id']) ?>;
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
          el.innerHTML = '<ul class="list-group">' + j.map(t =>
            `<li class="list-group-item d-flex justify-content-between align-items-center">
              <span>${t.created_at} — ${t.type}</span>
              <span>${t.amount} ${t.description ? '— ' + t.description : ''}</span>
            </li>`
          ).join('') + '</ul>';
        }
      }
    }
    refresh();
  </script>

</body>

</html>