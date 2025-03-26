<?php
require_once __DIR__ . '/../models/PedidoItens.php';

class PedidoItensController {
    private $pedido;

    public function __construct() {
        // Instancia a classe Pedido, que contém a lógica de manipulação dos itens
        $this->pedido = new Pedido();
    }

    public function listarPorPedido($pedido_id) {
        echo json_encode($this->pedido->buscarPorId($pedido_id)); // Verifica os itens de um pedido
    }

    public function adicionarItem() {
        $data = json_decode(file_get_contents("php://input"), true);
        if ($this->pedido->criar($data)) { // Método de criar pedido, que já adiciona itens
            echo json_encode(["mensagem" => "Item adicionado ao pedido"]);
        } else {
            echo json_encode(["erro" => "Erro ao adicionar item"]);
        }
    }

    public function atualizarItem($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        if ($this->pedido->atualizar($id, $data)) { // Atualiza o pedido
            echo json_encode(["mensagem" => "Item atualizado com sucesso"]);
        } else {
            echo json_encode(["erro" => "Erro ao atualizar item"]);
        }
    }

    public function removerItem($id) {
        if ($this->pedido->deletar($id)) { // Deleta o pedido com seus itens
            echo json_encode(["mensagem" => "Item removido com sucesso"]);
        } else {
            echo json_encode(["erro" => "Erro ao remover item"]);
        }
    }
}


