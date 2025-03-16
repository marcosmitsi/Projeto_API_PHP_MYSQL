<?php
require_once __DIR__ . '/../models/Pedido.php';

class PedidosController {
    private $pedido;

    public function __construct() {
        $this->pedido = new Pedido();
    }

    public function listar() {
        echo json_encode($this->pedido->listar());
    }

    public function buscar($id) {
        echo json_encode($this->pedido->buscar($id));
    }

    public function criar() {
        $data = json_decode(file_get_contents("php://input"), true);
        if ($this->pedido->criar($data)) {
            echo json_encode(["mensagem" => "Pedido criado com sucesso"]);
        } else {
            echo json_encode(["erro" => "Erro ao criar pedido"]);
        }
    }

    public function atualizar($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        if ($this->pedido->atualizar($id, $data)) {
            echo json_encode(["mensagem" => "Pedido atualizado com sucesso"]);
        } else {
            echo json_encode(["erro" => "Erro ao atualizar pedido"]);
        }
    }

    public function deletar($id) {
        if ($this->pedido->deletar($id)) {
            echo json_encode(["mensagem" => "Pedido deletado com sucesso"]);
        } else {
            echo json_encode(["erro" => "Erro ao deletar pedido"]);
        }
    }
}

