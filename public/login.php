<?php
// public/login.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = $_POST['email'] ?? '';
  $password = $_POST['password'] ?? '';
  $pdo = get_pdo();
  $stmt = $pdo->prepare("SELECT id,email,password,display_name,is_admin FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
  if ($user && password_verify($password, $user['password'])) {
    login_user($user);
    header('Location: /dashboard.php');
    exit;
  } else {
    $errors[] = "Credenciais inválidas.";
  }
}
?>
<!doctype html>
<html lang="pt-br">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>TigrIFBA - Entrar</title>

  <!-- Bootstrap core & SB Admin 2 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/css/sb-admin-2.min.css" rel="stylesheet">

  <!-- Custom styles -->
  <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body class="bg-gradient-primary">

  <div class="container">

    <!-- Outer Row -->
    <div class="row justify-content-center">

      <div class="col-xl-5 col-lg-6 col-md-8">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-0">
            <!-- Nested Row within Card Body -->
            <div class="p-5">
              <div class="text-center mb-4">
                <h1 class="h4 text-gray-900 mb-2">Bem-vindo ao TigrIFBA</h1>
                <p class="mb-4">Faça login para continuar</p>
              </div>

              <?php if ($errors): ?>
                <div class="alert alert-danger text-center" role="alert">
                  <?= htmlspecialchars($errors[0]) ?>
                </div>
              <?php endif; ?>

              <form method="post" class="user">
                <div class="form-group mb-3">
                  <input type="email" class="form-control form-control-user" name="email" placeholder="E-mail" required
                    autofocus>
                </div>
                <div class="form-group mb-3">
                  <input type="password" class="form-control form-control-user" name="password" placeholder="Senha"
                    required>
                </div>
                <button type="submit" class="btn btn-primary btn-user btn-block">
                  <i class="fas fa-sign-in-alt"></i> Entrar
                </button>
              </form>

              <hr>
              <div class="text-center">
                <a class="small" href="/register.php">Não tem conta? Registre-se</a>
              </div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/startbootstrap-sb-admin-2@4.1.4/js/sb-admin-2.min.js"></script>

</body>

</html>