<?php
require_once __DIR__ . '/../models/Produto.php';

class ProdutosController {
    private $produtoModel;

    public function __construct() {
        $this->produtoModel = new Produto();
    }

    public function listar() {
        echo json_encode($this->produtoModel->listar());
    }

    public function buscar($id) {
        echo json_encode($this->produtoModel->buscar($id));
    }

    public function criar() {
        $data = json_decode(file_get_contents("php://input"), true);
        if (strlen($data['descricao']) > 100) {
            echo json_encode(["erro" => "A descrição do produto deve ter no máximo 100 caracteres"]);
            exit;
        }
        if ($this->produtoModel->criar($data)) {
            echo json_encode(["mensagem" => "Produto criado com sucesso"]);
        } else {
            echo json_encode(["erro" => "Erro ao criar produto"]);
        }
    }

    public function atualizar($id) {
        $data = json_decode(file_get_contents("php://input"), true);
        if (strlen($data['descricao']) > 100) {
            echo json_encode(["erro" => "A descrição do produto deve ter no máximo 255 caracteres"]);
            exit;
        }
        if ($this->produtoModel->atualizar($id, $data)) {
            echo json_encode(["mensagem" => "Produto atualizado com sucesso"]);
        } else {
            echo json_encode(["erro" => "Erro ao atualizar produto"]);
        }
    }

    public function deletar($id) {
        if ($this->produtoModel->deletar($id)) {
            echo json_encode(["mensagem" => "Produto deletado com sucesso"]);
        } else {
            echo json_encode(["erro" => "Erro ao deletar produto"]);
        }
    }
}

