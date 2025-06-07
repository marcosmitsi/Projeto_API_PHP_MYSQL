<?php
require_once __DIR__ . '/../../config/database.php';

class PedidoItens {
    private $pdo;

    public function __construct() {
        $this->pdo = getConnection(); // Garante que estamos pegando a conexão corretamente
    }

    public function buscarPrecoProduto($produto_id) {
        $stmt = $this->pdo->prepare("SELECT preco FROM produtos WHERE id = ?");
        $stmt->execute([$produto_id]);
        $produto = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($produto) {
            return $produto['preco'];
        }

        return null; // Caso o produto não seja encontrado
    }

    public function adicionarItem($data) {
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $data['pedido_id'], 
                $data['produto_id'], 
                $data['quantidade'], 
                $data['preco']
            ]);

            return ["mensagem" => "Item adicionado ao pedido com sucesso"];
        } catch (PDOException $e) {
            return ["erro" => "Erro ao adicionar item: " . $e->getMessage()];
        }
    }

    public function listarPorPedido($pedido_id) {
        // ✅ Corrigido: faz JOIN com a tabela produtos para trazer o nome real do produto
        $stmt = $this->pdo->prepare("
            SELECT pi.*, p.nome AS nome_produto
            FROM pedido_itens pi
            JOIN produtos p ON pi.produto_id = p.id
            WHERE pi.pedido_id = ?
        ");
        $stmt->execute([$pedido_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function atualizarItem($id, $data) {
        try {
            $stmt = $this->pdo->prepare("
                UPDATE pedido_itens
                SET quantidade = ?, preco = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $data['quantidade'],
                $data['preco'],
                $id
            ]);

            return ["mensagem" => "Item atualizado com sucesso"];
        } catch (PDOException $e) {
            return ["erro" => "Erro ao atualizar item: " . $e->getMessage()];
        }
    }

    public function removerItem($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM pedido_itens WHERE id = ?");
            $stmt->execute([$id]);

            return ["mensagem" => "Item removido com sucesso"];
        } catch (PDOException $e) {
            return ["erro" => "Erro ao remover item: " . $e->getMessage()];
        }
    }
}
