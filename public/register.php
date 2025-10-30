<?php
// public/register.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
  $password = $_POST['password'] ?? '';
  $name = trim($_POST['name'] ?? '');
  if (!$email) $errors[] = "Email inválido.";
  if (strlen($password) < 6) $errors[] = "A senha deve ter ao menos 6 caracteres.";
  if (empty($errors)) {
    $pdo = get_pdo();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    try {
      $stmt = $pdo->prepare("INSERT INTO users (email,password,display_name) VALUES (?,?,?)");
      $stmt->execute([$email, $hash, $name]);
      $userId = $pdo->lastInsertId();
      $user = ['id' => $userId, 'email' => $email, 'display_name' => $name];
      login_user($user);
      header('Location: /dashboard.php');
      exit;
    } catch (PDOException $e) {
      if (strpos($e->getMessage(), 'UNIQUE') !== false) {
        $errors[] = "Email já cadastrado.";
      } else {
        $errors[] = "Erro ao salvar: " . $e->getMessage();
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registrar - Plataforma de Jogos Educativos</title>

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

      <div class="col-xl-6 col-lg-8 col-md-9">

        <div class="card o-hidden border-0 shadow-lg my-5">
          <div class="card-body p-5">
            <!-- Title -->
            <div class="text-center">
              <h1 class="h4 text-gray-900 mb-4">Criar Conta</h1>
            </div>

            <?php if ($errors): ?>
              <div class="alert alert-danger" role="alert">
                <?= implode('<br>', array_map('htmlspecialchars', $errors)) ?>
              </div>
            <?php endif; ?>

            <form method="post" class="user">
              <div class="form-group">
                <label for="name">Nome (opcional)</label>
                <input type="text" class="form-control form-control-user" id="name" name="name" placeholder="Seu nome"
                  value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
              </div>

              <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control form-control-user" id="email" name="email" placeholder="Email"
                  required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
              </div>

              <div class="form-group">
                <label for="password">Senha</label>
                <input type="password" class="form-control form-control-user" id="password" name="password"
                  placeholder="Senha" required>
              </div>

              <button type="submit" class="btn btn-primary btn-user btn-block">
                Registrar
              </button>
            </form>

            <hr>
            <div class="text-center">
              <a class="small" href="/login.php">Já tem uma conta? Entrar</a>
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