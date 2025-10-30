<?php
// src/api.php
define('API_VERSION', '0.0.2');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

// Simple API router based on ?action=...
header('Content-Type: application/json; charset=utf-8');

$action = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '';
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

switch ($action) {
  case '/status':
    if ($method !== 'GET') {
      http_response_code(405);
      echo json_encode(['error' => 'Método não permitido']);
      break;
    }

    // Verificar saúde do banco de dados
    $dbStatus = 'ok';
    try {
      $pdo->query("SELECT 1");
    } catch (Exception $e) {
      $dbStatus = 'error';
    }

    $response = [
      'status' => 'ok',
      'version' => API_VERSION,
      'timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
      'checks' => [
        'database' => $dbStatus,
        'logged_in' => is_logged()
      ]
    ];

    echo json_encode($response, JSON_UNESCAPED_SLASHES);
    break;

  case '/games':
    $stmt = $pdo->query("SELECT id,title,provider_url FROM games ORDER BY id DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    break;

  case '/balance':
    if (!is_logged()) {
      http_response_code(401);
      echo json_encode(['error' => 'Não autenticado']);
      exit;
    }
    $stmt = $pdo->prepare("SELECT balance FROM users WHERE id = ? LIMIT 1");
    $stmt->execute([get_user_id()]);
    $bal = $stmt->fetchColumn();

    echo json_encode([
      'balance' => (float)$bal,
      'last_updated' => gmdate('Y-m-d\TH:i:s\Z')
    ]);
    break;

  case '/debit':
  case '/credit':
    if ($method !== 'POST') {
      http_response_code(405);
      echo json_encode(['error' => 'Método não permitido']);
      break;
    }

    if (!is_logged()) {
      http_response_code(401);
      echo json_encode(['error' => 'Não autenticado']);
      exit;
    }

    $payload = json_decode(file_get_contents('php://input'), true);

    // Validar payload
    if (!isset($payload['amount']) || !is_numeric($payload['amount'])) {
      http_response_code(400);
      echo json_encode(['error' => 'Valor inválido']);
      exit;
    }

    $amount = (float)$payload['amount'];
    $game_id = $payload['game_id'] ?? null;
    $description = $payload['description'] ?? null;

    // Validar valor positivo
    if ($amount <= 0) {
      http_response_code(400);
      echo json_encode(['error' => 'Valor deve ser positivo']);
      exit;
    }

    // Para débito, converter para negativo
    if ($action === '/debit') {
      $amount = -$amount;
    }

    $pdo->beginTransaction();
    try {
      // Atualizar saldo do usuário verificando se o saldo é positivo
      $stmt = $pdo->prepare("UPDATE users SET balance = balance + ? WHERE id = ? AND balance + ? >= 0");
      $stmt->execute([$amount, get_user_id(), $amount]);

      // Verificar saldo insuficiente apenas para débito
      if ($stmt->rowCount() <= 0) {
        $pdo->rollBack();
        http_response_code(403);
        echo json_encode(['error' => 'Saldo insuficiente']);
        exit;
      }

      // Registrar transação
      $transaction_type = trim($action, '/');

      // Gerar descrição padrão se não fornecida
      if (!$description) {
        $description = ($action === '/debit' ? "Aposta no jogo" : "Prêmio do jogo");
        if ($game_id) {
          $stmt = $pdo->prepare("SELECT title FROM games WHERE id = ?");
          $stmt->execute([$game_id]);
          $game_title = $stmt->fetchColumn();
          $description .= " \"{$game_title}\"";
        }
      }

      $stmt = $pdo->prepare("INSERT INTO transactions (user_id, amount, type, description) VALUES (?, ?, ?, ?)");
      $stmt->execute([
        get_user_id(),
        $amount,
        $transaction_type,
        $description
      ]);

      $pdo->commit();

      // Retornar resposta de sucesso
      echo json_encode([
        'success' => true,
        'message' => 'Saldo atualizado com sucesso.'
      ]);
    } catch (Exception $e) {
      $pdo->rollBack();
      http_response_code(500);
      echo json_encode(['error' => 'Erro interno do servidor: ' . $e->getMessage()]);
    }
    break;

  case '/transactions':
    if (!is_logged()) {
      http_response_code(401);
      echo json_encode(['error' => 'Não autenticado']);
      exit;
    }
    $stmt = $pdo->prepare("SELECT id,amount,type,description,created_at FROM transactions WHERE user_id = ? ORDER BY created_at DESC LIMIT 50");
    $stmt->execute([get_user_id()]);
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    break;

  default:
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint não encontrado']);
}
