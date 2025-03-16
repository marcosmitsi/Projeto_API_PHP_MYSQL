<?php
require_once __DIR__ . '/../../config/database.php';

class Pedido {
    private $pdo;

    public function __construct() {
        $this->pdo = getConnection(); // Agora chamamos a função correta!
    }

    public function listar() {
        $query = "SELECT * FROM pedidos";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar($id) {
        $query = "SELECT * FROM pedidos WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function criar($data) {
        $query = "INSERT INTO pedidos (cliente_id, total, status_pedido) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$data['cliente_id'], $data['total'], $data['status_pedido']]);
    }

    public function atualizar($id, $data) {
        $query = "UPDATE pedidos SET cliente_id = ?, total = ?, status_pedido = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$data['cliente_id'], $data['total'], $data['status_pedido'], $id]);
    }

    public function deletar($id) {
        $query = "DELETE FROM pedidos WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}

