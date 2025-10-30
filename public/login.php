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
<html>

<head>
  <meta charset="utf-8">
  <title>Entrar - Plataforma</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
  <div class="card">
    <h1>Entrar</h1>
    <?php if ($errors): ?><div class="errors"><?= htmlspecialchars($errors[0]) ?></div><?php endif; ?>
    <form method="post">
      <label>Email<input type="email" name="email" required></label>
      <label>Senha<input type="password" name="password" required></label>
      <button type="submit">Entrar</button>
    </form>
    <p>Sem conta? <a href="/register.php">Registrar</a></p>
  </div>
</body>

</html>