<?php
require_once 'config/database.php';
require_once 'config/constants.php';
require_once 'models/Order.php';

header('Content-Type: application/json');

// Verifica o método da requisição
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método não permitido']);
    exit;
}

// Verifica a chave de autenticação
$apiKey = isset($_SERVER['HTTP_X_API_KEY']) ? $_SERVER['HTTP_X_API_KEY'] : '';
if ($apiKey !== WEBHOOK_KEY) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

// Obtém os dados do webhook
$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['order_id']) || !isset($input['status'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Dados inválidos']);
    exit;
}

$orderId = (int)$input['order_id'];
$status = strtolower(trim($input['status']));

// Conexão com o banco de dados
$database = new Database();
$db = $database->connect();
$orderModel = new Order($db);

// Verifica se o pedido existe
$order = $orderModel->readById($orderId);
if (!$order) {
    http_response_code(404);
    echo json_encode(['error' => 'Pedido não encontrado']);
    exit;
}

// Status válidos
$validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
if (!in_array($status, $validStatuses)) {
    http_response_code(400);
    echo json_encode(['error' => 'Status inválido']);
    exit;
}

// Atualiza o status do pedido
if ($orderModel->updateStatus($orderId, $status)) {
    echo json_encode(['message' => 'Status do pedido atualizado com sucesso']);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao atualizar status do pedido']);
}
?>