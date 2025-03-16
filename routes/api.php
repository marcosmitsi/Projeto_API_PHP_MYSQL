<?php


require_once __DIR__ . '/../app/controllers/ProdutosController.php';
require_once __DIR__ . '/../app/controllers/PedidosController.php';
require_once __DIR__ . '/../app/controllers/PedidoItensController.php';
require_once __DIR__ . '/../app/controllers/ClientesController.php';

// Ajuste da URL base
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = str_replace('/kidelicia/public', '', $uri);  // Remover '/kidelicia/public'
$uri = rtrim($uri, '/');  // Remover barra no final, caso exista
$method = $_SERVER['REQUEST_METHOD'];
// Exibir a URL e método para depuração
//var_dump($uri, $_SERVER['REQUEST_METHOD']); 
//die('Parou aqui');



$produtosController = new ProdutosController();
$pedidosController = new PedidosController();
$pedidoItensController = new PedidoItensController();
$clientesController = new ClientesController();

// Rotas para produtos
// Verificação da rota de produtos
/*
if ($uri == '/api/produtos' && $method == 'GET') {
    $produtosController->listar();
} elseif (preg_match('/\/api\/produtos\/(\d+)/', $uri, $matches) && $method == 'GET') {
    $produtosController->buscar($matches[1]);
} elseif ($uri == '/api/produtos' && $method == 'POST') {
    $produtosController->criar();
} elseif (preg_match('/\/api\/produtos\/(\d+)/', $uri, $matches) && $method == 'PUT') {
    $produtosController->atualizar($matches[1]);
} elseif (preg_match('/\/api\/produtos\/(\d+)/', $uri, $matches) && $method == 'DELETE') {
    $produtosController->deletar($matches[1]);
}
*/
// Verificar se a URI é exatamente '/api/produtos' com método GET
if ($uri == '/api/produtos' && $method == 'GET') {
   // echo "Rota de produtos encontrada!";
    $produtosController->listar(); // Chama o método listar() para retornar os produtos
    exit; // Adicionando exit para evitar que o código continue após a resposta
} elseif (preg_match('/\/api\/produtos\/(\d+)/', $uri, $matches) && $method == 'GET') {
   echo "Rota de produto específica encontrada!";
    $produtosController->buscar($matches[1]);
} elseif ($uri == '/api/produtos' && $method == 'POST') {
    echo "Rota POST de produtos encontrada!";
    $produtosController->criar();
} elseif (preg_match('/\/api\/produtos\/(\d+)/', $uri, $matches) && $method == 'PUT') {
    echo "Rota PUT de produto encontrada!";
    $produtosController->atualizar($matches[1]);
} elseif (preg_match('/\/api\/produtos\/(\d+)/', $uri, $matches) && $method == 'DELETE') {
    echo "Rota DELETE de produto encontrada!";
    $produtosController->deletar($matches[1]);
} else {
    echo "Rota não encontrada!";
}


// Rotas para pedidos
if ($uri == '/api/pedidos' && $method == 'GET') {
    $pedidosController->listar();
} elseif (preg_match('/\/api\/pedidos\/(\d+)/', $uri, $matches) && $method == 'GET') {
    $pedidosController->buscar($matches[1]);
} elseif ($uri == '/api/pedidos' && $method == 'POST') {
    $pedidosController->criar();
} elseif (preg_match('/\/api\/pedidos\/(\d+)/', $uri, $matches) && $method == 'PUT') {
    $pedidosController->atualizar($matches[1]);
} elseif (preg_match('/\/api\/pedidos\/(\d+)/', $uri, $matches) && $method == 'DELETE') {
    $pedidosController->deletar($matches[1]);
}

// Rotas para itens do pedido
if (preg_match('/\/api\/pedido-itens\/pedido\/(\d+)/', $uri, $matches) && $method == 'GET') {
    $pedidoItensController->listarPorPedido($matches[1]);
} elseif ($uri == '/api/pedido-itens' && $method == 'POST') {
    $pedidoItensController->adicionarItem();
} elseif (preg_match('/\/api\/pedido-itens\/(\d+)/', $uri, $matches) && $method == 'PUT') {
    $pedidoItensController->atualizarItem($matches[1]);
} elseif (preg_match('/\/api\/pedido-itens\/(\d+)/', $uri, $matches) && $method == 'DELETE') {
    $pedidoItensController->removerItem($matches[1]);
}

// Rotas para clientes
if ($uri == '/api/clientes' && $method == 'GET') {
    $clientesController->listar();
} elseif (preg_match('/\/api\/clientes\/(\d+)/', $uri, $matches) && $method == 'GET') {
    $clientesController->buscar($matches[1]);
} elseif ($uri == '/api/clientes' && $method == 'POST') {
    $clientesController->criar();
} elseif (preg_match('/\/api\/clientes\/(\d+)/', $uri, $matches) && $method == 'PUT') {
    $clientesController->atualizar($matches[1]);
} elseif (preg_match('/\/api\/clientes\/(\d+)/', $uri, $matches) && $method == 'DELETE') {
    $clientesController->deletar($matches[1]);
} else {
    http_response_code(404);
    echo json_encode(["erro" => "Rota não encontrada"]);
}

