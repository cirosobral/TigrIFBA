<?php
// src/db.php
function get_db_path()
{
  return __DIR__ . '/../data/db.sqlite';
}

function get_pdo()
{
  $path = get_db_path();
  $needInit = !file_exists($path);
  $pdo = new PDO('sqlite:' . $path);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  return $pdo;
}
