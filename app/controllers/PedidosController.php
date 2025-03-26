<?php
require_once __DIR__ . '/../models/Pedido.php';
require_once __DIR__ . '/../models/PedidoItens.php';

class PedidosController {
    private $pedidoModel;
    private $pedidoItensModel;

    public function __construct() {
        $this->pedidoModel = new Pedido();
        $this->pedidoItensModel = new PedidoItens();
    }

    public function criar() {
        $data = json_decode(file_get_contents("php://input"), true);
    
        if (!isset($data['cliente_id'], $data['itens']) || !is_array($data['itens'])) {
            echo json_encode(["erro" => "Cliente ID e itens são obrigatórios"]);
            http_response_code(400);
            exit;
        }
    
        // Criar o pedido principal
        $resultado = $this->pedidoModel->criar($data);  // Recebe o array com "mensagem" e "pedido_id"
        if (isset($resultado['erro'])) {  // Verifica se ocorreu um erro
            echo json_encode(["erro" => $resultado['erro']]);
            http_response_code(500);
            exit;
        }
    
        $pedidoId = $resultado['pedido_id'];  // Acessa o pedido_id do array retornado
    
        // Inserir os itens do pedido
        foreach ($data['itens'] as $item) {
            if (!isset($item['produto_id'], $item['quantidade'])) {
                echo json_encode(["erro" => "Cada item precisa de produto_id e quantidade"]);
                http_response_code(400);
                exit;
            }
    
            // Buscar preço do produto
            $preco = $this->pedidoItensModel->buscarPrecoProduto($item['produto_id']);
            if (!$preco) {
                echo json_encode(["erro" => "Produto ID {$item['produto_id']} não encontrado."]);
                http_response_code(400);
                exit;
            }
    
            $this->pedidoItensModel->adicionarItem([
                "pedido_id" => $pedidoId,
                "produto_id" => $item['produto_id'],
                "quantidade" => $item['quantidade'],
                "preco" => $preco
            ]);
        }
    
        echo json_encode(["mensagem" => "Pedido criado com sucesso", "pedido_id" => $pedidoId]);
    }

    public function buscar($id = null) {
        if ($id) {
            $this->buscarPorId($id);
        } else {
            $this->buscarTodos();
        }
    }

    public function buscarPorId($id) {
        $pedido = $this->pedidoModel->buscarPorId($id);
        if ($pedido) {
            echo json_encode($pedido);
        } else {
            http_response_code(404);
            echo json_encode(["erro" => "Pedido não encontrado."]);
        }
    }

    public function buscarTodos() {
        $pedidos = $this->pedidoModel->buscarTodos();
        echo json_encode($pedidos);
    }

    public function atualizar($id) {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['cliente_id'], $data['itens']) || !is_array($data['itens'])) {
            echo json_encode(["erro" => "Cliente ID e itens são obrigatórios"]);
            http_response_code(400);
            return;
        }

        $resultado = $this->pedidoModel->atualizar($id, $data);
        if (isset($resultado['erro'])) {
            echo json_encode(["erro" => $resultado['erro']]);
            http_response_code(500);
            return;
        }

        echo json_encode(["mensagem" => "Pedido atualizado com sucesso"]);
    }

    // Deletar um pedido existente
    public function deletar($id) {
        $resultado = $this->pedidoModel->deletar($id);
        if (isset($resultado['erro'])) {
            echo json_encode(["erro" => $resultado['erro']]);
            http_response_code(500);
            return;
        }

        echo json_encode(["mensagem" => "Pedido deletado com sucesso"]);
    }


    
}