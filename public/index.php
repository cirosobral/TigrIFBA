<?php
// public/index.php
require_once __DIR__ . '/../src/auth.php';
if (is_logged()) {
  header('Location: /dashboard.php');
} else {
  header('Location: /login.php');
}
exit;
