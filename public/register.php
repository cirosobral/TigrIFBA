<?php
// public/register.php
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';

$errors = [];
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';
    $name = trim($_POST['name'] ?? '');
    if (!$email) $errors[] = "Email inválido.";
    if (strlen($password) < 6) $errors[] = "Senha deve ter ao menos 6 caracteres.";
    if (empty($errors)) {
        $pdo = get_pdo();
        $hash = password_hash($password, PASSWORD_DEFAULT);
        try {
            $stmt = $pdo->prepare("INSERT INTO users (email,password,display_name) VALUES (?,?,?)");
            $stmt->execute([$email, $hash, $name]);
            $userId = $pdo->lastInsertId();
            $user = ['id'=>$userId,'email'=>$email,'display_name'=>$name];
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
<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>Registrar - Plataforma de Jogos Educativos</title>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
  <div class="card">
    <h1>Registrar</h1>
    <?php if ($errors): ?>
    <div class="errors"><?=implode('<br>', array_map('htmlspecialchars',$errors))?></div>
    <?php endif; ?>
    <form method="post">
      <label>Nome (opcional)<input type="text" name="name" value="<?=htmlspecialchars($_POST['name'] ?? '')?>"></label>
      <label>Email<input type="email" name="email" value="<?=htmlspecialchars($_POST['email'] ?? '')?>"
          required></label>
      <label>Senha<input type="password" name="password" required></label>
      <button type="submit">Registrar</button>
    </form>
    <p>Já tem conta? <a href="/login.php">Entrar</a></p>
  </div>
</body>

</html>