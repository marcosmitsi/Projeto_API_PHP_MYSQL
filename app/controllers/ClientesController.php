<?php
require_once __DIR__ . '/../models/Cliente.php';

class ClientesController {
    private $clienteModel;

    public function __construct() {
        $this->clienteModel = new Cliente();
    }

    public function listar() {
        echo json_encode($this->clienteModel->listar());
    }

    public function buscar($id) {
        echo json_encode($this->clienteModel->buscar($id));
    }

    public function criar() {
        $data = json_decode(file_get_contents("php://input"), true);
        $this->clienteModel->criar($data);
        echo json_encode(["mensagem" => "Cliente criado com sucesso"]);
    }

    public function atualizar($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        $this->clienteModel->atualizar($id, $data);
        echo json_encode(["mensagem" => "Cliente atualizado com sucesso"]);
    }

    public function deletar($id) {
        $this->clienteModel->deletar($id);
        echo json_encode(["mensagem" => "Cliente deletado com sucesso"]);
    }
}
