<?php
// public/play.php
require_once __DIR__ . '/../src/auth.php';
require_once __DIR__ . '/../src/db.php';
require_login();

$pdo = get_pdo();
$id = intval($_GET['id'] ?? 0);

// busca informações do jogo
$stmt = $pdo->prepare("SELECT id, title, provider_url FROM games WHERE id = ?");
$stmt->execute([$id]);
$game = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$game) {
  http_response_code(404);
  echo "Jogo não encontrado.";
  exit;
}

// busca saldo e dados do usuário logado
$stmt = $pdo->prepare("SELECT display_name, email, balance FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($game['title']) ?> - Plataforma Educativa</title>

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

      <li class="nav-item">
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

          <!-- Título -->
          <h5 class="text-primary font-weight-bold mb-0"><?= htmlspecialchars($game['title']) ?></h5>

          <!-- Topbar Navbar -->
          <ul class="navbar-nav ms-auto">

            <!-- Saldo -->
            <li class="nav-item dropdown no-arrow mx-1">
              <a class="nav-link" href="#" role="button">
                <i class="fas fa-coins text-warning"></i>
                <span class="mr-2 d-none d-lg-inline text-gray-800">
                  Saldo: R$ <?= number_format($user['balance'], 0, ',', '.') ?>
                </span>
              </a>
            </li>

            <div class="topbar-divider d-none d-sm-block"></div>

            <!-- Usuário -->
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

              <!-- Dropdown -->
              <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                <a class="dropdown-item" href="/dashboard.php">
                  <i class="fas fa-home fa-sm fa-fw mr-2 text-gray-400"></i>
                  Dashboard
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="/logout.php">
                  <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                  Sair
                </a>
              </div>
            </li>
          </ul>
        </nav>
        <!-- End of Topbar -->

        <!-- Conteúdo -->
        <div class="container-fluid px-0" style="height: calc(100vh - 100px);">

          <!-- Iframe ocupa toda a área -->
          <div class="embed-responsive embed-responsive-16by9" style="height:100%;">
            <iframe id="gameFrame" src="<?= htmlspecialchars($game['provider_url']) ?>"
              title="<?= htmlspecialchars($game['title']) ?>" sandbox="allow-scripts allow-forms allow-same-origin"
              allowfullscreen style="border:0;width:100%;height:100%;"></iframe>
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

</body>

</html>