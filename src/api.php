<?php
// src/api.php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

// Simple API router based on ?action=...
header('Content-Type: application/json; charset=utf-8');

$action = array_filter(explode('/', $_SERVER['REQUEST_URI'])) ?? [];
$method = $_SERVER['REQUEST_METHOD'] ?? '';

$pdo = get_pdo();

// CORS for dev (adjust in production)
if ($method === 'OPTIONS') {
  header('Access-Control-Allow-Origin: *');
  header('Access-Control-Allow-Methods: GET,POST,OPTIONS');
  header('Access-Control-Allow-Headers: Content-Type');
  exit;
}
header('Access-Control-Allow-Origin: *');

switch (array_shift($action)) {
  case 'games':
    $stmt = $pdo->query("SELECT id,title,provider_url FROM games ORDER BY id DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    break;

  case 'balance':
    if (!is_logged()) {
      http_response_code(401);
      echo json_encode(['error' => 'unauthorized']);
      exit;
    }
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $bal = $stmt->fetchColumn();
    echo json_encode(['balance' => (int)$bal]);
    break;

  case 'transactions':
    if (!is_logged()) {
      http_response_code(401);
      echo json_encode(['error' => 'unauthorized']);
      exit;
    }
    $stmt = $pdo->prepare("SELECT id,amount,type,description,created_at FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
    $stmt->execute([$_SESSION['user_id']]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    break;

  case 'bet':
    // POST {amount, type, description}
    if (!is_logged()) {
      http_response_code(401);
      echo json_encode(['error' => 'unauthorized']);
      exit;
    }
    $payload = json_decode(file_get_contents('php://input'), true);
    $amount = isset($payload['amount']) ? (int)$payload['amount'] : 0;
    $type = $payload['type'] ?? 'bet';
    $desc = $payload['description'] ?? null;
    if ($amount === 0) {
      http_response_code(400);
      echo json_encode(['error' => 'invalid amount']);
      exit;
    }

    // For simplicity: negative amounts are debit, positive are credit
    $pdo->beginTransaction();
    try {
      // Update balance
      $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ? FOR UPDATE");
      $stmt->execute([$_SESSION['user_id']]);
      $bal = (int)$stmt->fetchColumn();
      $newBal = $bal + $amount;
      if ($newBal < 0) {
        $pdo->rollBack();
        http_response_code(400);
        echo json_encode(['error' => 'Saldo insuficiente']);
        exit;
      }
      $pdo->prepare("UPDATE users SET balance = ? WHERE id = ?")->execute([$newBal, $_SESSION['user_id']]);
      $pdo->prepare("INSERT INTO transactions (user_id,amount,type,description) VALUES (?,?,?,?)")
        ->execute([$_SESSION['user_id'], $amount, $type, $desc]);
      $pdo->commit();
      echo json_encode(['balance' => $newBal]);
    } catch (Exception $e) {
      $pdo->rollBack();
      http_response_code(500);
      echo json_encode(['error' => $e->getMessage()]);
    }
    break;

  default:
    http_response_code(404);
    echo json_encode(['error' => 'action not found']);
}
