<?php
session_start();
require_once 'config/database.php';
require_once 'config/functions.php';
require_once 'config/constants.php';

$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? null;
$id = $_GET['id'] ?? null;

// Conexão com o banco de dados
$database = new Database();
$db = $database->connect();

// Mapeamento de páginas para controladores
$controllers = [
    'home' => null,
    'products' => 'ProductController',
    'cart' => 'CartController',
    'orders' => 'OrderController',
    'coupons' => 'CouponController'
];

// Verifica se a página solicitada existe
if (!array_key_exists($page, $controllers)) {
    http_response_code(404);
    echo "Página não encontrada";
    exit;
}

// Inicializa o controlador se existir
$controllerName = $controllers[$page];
if ($controllerName) {
    require_once "controllers/{$controllerName}.php";
    $controller = new $controllerName();
}

// Executa a ação correspondente
switch ($action) {
    case 'create':
        $controller->create();
        break;
    case 'edit':
        $controller->edit($id);
        break;
    case 'delete':
        $controller->delete($id);
        break;
    case 'add':
        $controller->add();
        break;
    case 'remove':
        $controller->remove($id);
        break;
    case 'update':
        $controller->update();
        break;
    case 'checkout_form':  // Nova ação para exibir o formulário
        $controller->checkoutForm();
        break;
    case 'checkout':       // Ação para processar o checkout
        $controller->checkout();
        break;
    case 'apply_coupon':
        $controller->applyCoupon();
        break;
    case 'remove_coupon':
        unset($_SESSION['coupon']);
        header('Location: ?page=cart');
        exit;
    case 'count':
        header('Content-Type: application/json');
        echo json_encode(['count' => count($_SESSION['cart'] ?? [])]);
        exit;
    case 'calculate_shipping':  // Padronizado o nome da ação
        $controller->calculateShippingByCep();
        break;
    case 'save_shipping':      // Padronizado o nome da ação
        $controller->saveShipping();
        break;
    case 'update_status':      // Nova ação para atualizar status do pedido
        $controller->update_status($id, $_GET['status']);
        break;
    default:
        if ($controllerName) {
            // Verifica métodos na ordem: view(), index()
            if (method_exists($controller, 'view')) {
                $controller->view();
            } elseif (method_exists($controller, 'index')) {
                $controller->index();
            } else {
                include 'views/errors/404.php';
            }
        } else {
            include 'views/home.php';
        }
        break;
}