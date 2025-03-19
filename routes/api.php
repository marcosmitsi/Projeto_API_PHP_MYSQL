<?php
header("Content-Type: application/json"); // Garante que a resposta seja JSON

require_once __DIR__ . '/../app/controllers/ProdutosController.php';
require_once __DIR__ . '/../app/controllers/PedidosController.php';
require_once __DIR__ . '/../app/controllers/PedidoItensController.php';
require_once __DIR__ . '/../app/controllers/ClientesController.php';

// Ajuste da URL base
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace('/kidelicia/public', '', $uri);  // Remover '/kidelicia/public'
$uri = rtrim($uri, '/');  // Remover barra no final, caso exista
$method = $_SERVER['REQUEST_METHOD'];

// Exibir a URL e método para depuração (ative para testar)
// var_dump($uri, $method); die(); 

$produtosController = new ProdutosController();
$pedidosController = new PedidosController();
$pedidoItensController = new PedidoItensController();
$clientesController = new ClientesController();

// Rotas para produtos
if ($uri == '/api/produtos' && $method == 'GET') {
    $produtosController->listar();
    exit;
} elseif (preg_match('/\/api\/produtos\/(\d+)/', $uri, $matches) && $method == 'GET') {
    $produtosController->buscar($matches[1]);
    exit;
} elseif ($uri == '/api/produtos' && $method == 'POST') {
    $produtosController->criar();
    exit;
} elseif (preg_match('/\/api\/produtos\/(\d+)/', $uri, $matches) && $method == 'PUT') {
    $produtosController->atualizar($matches[1]);
    exit;
} elseif (preg_match('/\/api\/produtos\/(\d+)/', $uri, $matches) && $method == 'DELETE') {
    $produtosController->deletar($matches[1]);
    exit;
}

// Rotas para pedidos
if ($uri == '/api/pedidos' && $method == 'GET') {
    $pedidosController->listar();
    exit;
} elseif (preg_match('/\/api\/pedidos\/(\d+)/', $uri, $matches) && $method == 'GET') {
    $pedidosController->buscar($matches[1]);
    exit;
} elseif ($uri == '/api/pedidos' && $method == 'POST') {
    $pedidosController->criar();
    exit;
} elseif (preg_match('/\/api\/pedidos\/(\d+)/', $uri, $matches) && $method == 'PUT') {
    $pedidosController->atualizar($matches[1]);
    exit;
} elseif (preg_match('/\/api\/pedidos\/(\d+)/', $uri, $matches) && $method == 'DELETE') {
    $pedidosController->deletar($matches[1]);
    exit;
}

// Rotas para itens do pedido
if (preg_match('/\/api\/pedido-itens\/pedido\/(\d+)/', $uri, $matches) && $method == 'GET') {
    $pedidoItensController->listarPorPedido($matches[1]);
    exit;
} elseif ($uri == '/api/pedido-itens' && $method == 'POST') {
    $pedidoItensController->adicionarItem();
    exit;
} elseif (preg_match('/\/api\/pedido-itens\/(\d+)/', $uri, $matches) && $method == 'PUT') {
    $pedidoItensController->atualizarItem($matches[1]);
    exit;
} elseif (preg_match('/\/api\/pedido-itens\/(\d+)/', $uri, $matches) && $method == 'DELETE') {
    $pedidoItensController->removerItem($matches[1]);
    exit;
}

// Rotas para clientes
if ($uri == '/api/clientes' && $method == 'GET') {
    $clientesController->listar();
    exit;
} elseif (preg_match('/\/api\/clientes\/(\d+)/', $uri, $matches) && $method == 'GET') {
    $clientesController->buscar($matches[1]);
    exit;
} elseif ($uri == '/api/clientes' && $method == 'POST') {
    $clientesController->criar();
    exit;
} elseif (preg_match('/\/api\/clientes\/(\d+)/', $uri, $matches) && $method == 'PUT') {
    $clientesController->atualizar($matches[1]);
    exit;
} elseif (preg_match('/\/api\/clientes\/(\d+)/', $uri, $matches) && $method == 'DELETE') {
    $clientesController->deletar($matches[1]);
    exit;
}

// Se nenhuma rota foi encontrada
http_response_code(404);
echo json_encode(["erro" => "Rota não encontrada"]);
exit;