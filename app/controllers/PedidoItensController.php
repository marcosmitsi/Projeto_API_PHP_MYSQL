<?php
require_once __DIR__ . '/../models/PedidoItens.php';

class PedidoItensController {
    private $pedidoItens;

    public function __construct() {
        $this->pedidoItens = new PedidoItens();
    }

    public function listarPorPedido($pedido_id) {
        echo json_encode($this->pedidoItens->listarPorPedido($pedido_id));
    }

    public function adicionarItem() {
        $data = json_decode(file_get_contents("php://input"), true);
        if ($this->pedidoItens->adicionarItem($data)) {
            echo json_encode(["mensagem" => "Item adicionado ao pedido"]);
        } else {
            echo json_encode(["erro" => "Erro ao adicionar item"]);
        }
    }

    public function atualizarItem($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        if ($this->pedidoItens->atualizarItem($id, $data)) {
            echo json_encode(["mensagem" => "Item atualizado com sucesso"]);
        } else {
            echo json_encode(["erro" => "Erro ao atualizar item"]);
        }
    }

    public function removerItem($id) {
        if ($this->pedidoItens->removerItem($id)) {
            echo json_encode(["mensagem" => "Item removido com sucesso"]);
        } else {
            echo json_encode(["erro" => "Erro ao remover item"]);
        }
    }
}

