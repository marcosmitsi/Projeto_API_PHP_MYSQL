<?php
require_once __DIR__ . '/../../config/database.php';

class Produto {
    private $pdo;

    public function __construct() {
        $this->pdo = getConnection(); 
    }

    // Lista todos os produtos
    public function buscarTodos() {
        $stmt = $this->pdo->query("SELECT * FROM produtos ORDER BY nome ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Busca um único produto pelo ID
    public function buscarPorId($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM produtos WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cria um novo produto
    public function criar($data) {
        $stmt = $this->pdo->prepare("INSERT INTO produtos (nome, descricao, preco, estoque) VALUES (?, ?, ?, ?)");
        return $stmt->execute([
            $data['nome'],
            $data['descricao'],
            $data['preco'],
            $data['estoque']
        ]);
    }

    // Atualiza um produto existente
    public function atualizar($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE produtos SET nome = ?, descricao = ?, preco = ?, estoque = ? WHERE id = ?");
        return $stmt->execute([
            $data['nome'],
            $data['descricao'],
            $data['preco'],
            $data['estoque'],
            $id
        ]);
    }

    // Deleta um produto
    public function deletar($id) {
        $stmt = $this->pdo->prepare("DELETE FROM produtos WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
