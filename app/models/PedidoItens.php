<?php
require_once __DIR__ . '/../../config/database.php';

class PedidoItens {
    private $pdo;

    public function __construct() {
        $this->pdo = getConnection(); // Agora chamamos a função correta!
    }

    public function listarPorPedido($pedido_id) {
        $query = "SELECT * FROM pedido_itens WHERE pedido_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$pedido_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function adicionarItem($data) {
        $query = "INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$data['pedido_id'], $data['produto_id'], $data['quantidade'], $data['preco_unitario']]);
    }

    public function atualizarItem($id, $data) {
        $query = "UPDATE pedido_itens SET quantidade = ?, preco_unitario = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$data['quantidade'], $data['preco_unitario'], $id]);
    }

    public function removerItem($id) {
        $query = "DELETE FROM pedido_itens WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}

