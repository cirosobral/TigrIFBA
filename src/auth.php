<?php
// src/auth.php
session_start();

function is_logged()
{
  return !empty(get_user_id());
}

function is_admin()
{
  return !empty($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
}

function require_login()
{
  if (!is_logged()) {
    header('Location: /login.php');
    exit;
  }
}

function require_admin()
{
  require_login();
  if (!is_admin()) {
    http_response_code(403);
    echo "<h1>Acesso negado</h1>";
    exit;
  }
}

function login_user($user)
{
  $_SESSION['user_id'] = $user['id'];
  $_SESSION['email'] = $user['email'];
  $_SESSION['display_name'] = $user['display_name'] ?? $user['email'];
  $_SESSION['is_admin'] = $user['is_admin'] ?? 0;
}

function get_user_id()
{
  return $_SESSION['user_id'];
}

function logout_user()
{
  session_unset();
  session_destroy();
}
