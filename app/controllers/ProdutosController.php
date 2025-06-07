<?php
require_once __DIR__ . '/../models/Produto.php';

class ProdutosController {
    private $produtoModel;

    public function __construct() {
        $this->produtoModel = new Produto();
    }

    // Lista todos os produtos
    public function listar() {
        $produtos = $this->produtoModel->buscarTodos();
        echo json_encode($produtos);
    }

    // Busca um produto pelo ID
    public function buscar($id) {
        $produto = $this->produtoModel->buscarPorId($id);
        if ($produto) {
            echo json_encode($produto);
        } else {
            http_response_code(404);
            echo json_encode(["erro" => "Produto não encontrado"]);
        }
    }

    // Cria um novo produto
    public function criar() {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['nome'], $data['descricao'], $data['preco'], $data['estoque'])) {
            http_response_code(400);
            echo json_encode(["erro" => "Todos os campos são obrigatórios"]);
            return;
        }

        if (strlen($data['descricao']) > 255) {
            http_response_code(400);
            echo json_encode(["erro" => "A descrição do produto deve ter no máximo 255 caracteres"]);
            return;
        }

        $resultado = $this->produtoModel->criar($data);
        if ($resultado) {
            echo json_encode(["mensagem" => "Produto criado com sucesso"]);
        } else {
            http_response_code(500);
            echo json_encode(["erro" => "Erro ao criar produto"]);
        }
    }

    // Atualiza um produto
    public function atualizar($id) {
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['nome'], $data['descricao'], $data['preco'], $data['estoque'])) {
            http_response_code(400);
            echo json_encode(["erro" => "Todos os campos são obrigatórios"]);
            return;
        }

        if (strlen($data['descricao']) > 255) {
            http_response_code(400);
            echo json_encode(["erro" => "A descrição do produto deve ter no máximo 255 caracteres"]);
            return;
        }

        $resultado = $this->produtoModel->atualizar($id, $data);
        if ($resultado) {
            echo json_encode(["mensagem" => "Produto atualizado com sucesso"]);
        } else {
            http_response_code(500);
            echo json_encode(["erro" => "Erro ao atualizar produto"]);
        }
    }

    // Deleta um produto
    public function deletar($id) {
        $resultado = $this->produtoModel->deletar($id);
        if ($resultado) {
            echo json_encode(["mensagem" => "Produto deletado com sucesso"]);
        } else {
            http_response_code(500);
            echo json_encode(["erro" => "Erro ao deletar produto"]);
        }
    }
}
