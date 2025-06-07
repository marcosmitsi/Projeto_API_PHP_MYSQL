<?php  
require_once __DIR__ . '/../../config/database.php';

class Pedido {
    private $pdo;

    public function __construct() {
        $this->pdo = getConnection();
    }

    public function criar($data) {
        try {
            $this->pdo->beginTransaction();

            $valoresValidos = ['pendente', 'processando', 'concluido', 'cancelado'];
            $status = $data['status_pedido'] ?? 'pendente';
            if (!in_array($status, $valoresValidos)) {
                throw new Exception("Status inválido: '$status'.");
            }

            $stmt = $this->pdo->prepare("INSERT INTO pedidos (cliente_id, total, status_pedido) VALUES (?, ?, ?)");
            $stmt->execute([$data['cliente_id'], 0, $status]);
            $pedido_id = $this->pdo->lastInsertId();

            $total = 0;

            if (isset($data['itens']) && is_array($data['itens']) && !empty($data['itens'])) {
                foreach ($data['itens'] as $item) {
                    if (!isset($item['produto_id']) || !isset($item['quantidade'])) {
                        throw new Exception("Formato de item inválido.");
                    }

                    $stmt = $this->pdo->prepare("SELECT preco FROM produtos WHERE id = ?");
                    $stmt->execute([$item['produto_id']]);
                    $produto = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$produto) {
                        throw new Exception("Produto ID {$item['produto_id']} não encontrado.");
                    }

                    $subtotal = $produto['preco'] * $item['quantidade'];
                    $total += $subtotal;

                    $stmt = $this->pdo->prepare("INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$pedido_id, $item['produto_id'], $item['quantidade'], $produto['preco'], $subtotal]);
                }
            } else {
                throw new Exception("Nenhum item encontrado no pedido.");
            }

            $stmt = $this->pdo->prepare("UPDATE pedidos SET total = ? WHERE id = ?");
            $stmt->execute([$total, $pedido_id]);

            $this->pdo->commit();
            return ["mensagem" => "Pedido criado com sucesso!", "pedido_id" => $pedido_id];

        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ["erro" => $e->getMessage()];
        }
    }

    public function buscarPorId($id) {
        $stmt = $this->pdo->prepare("SELECT p.*, c.nome AS nome_cliente FROM pedidos p JOIN clientes c ON p.cliente_id = c.id WHERE p.id = ?");
        $stmt->execute([$id]);
        $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($pedido) {
            $stmt = $this->pdo->prepare("SELECT pi.*, pr.nome AS nome_produto FROM pedido_itens pi JOIN produtos pr ON pi.produto_id = pr.id WHERE pi.pedido_id = ?");
            $stmt->execute([$id]);
            $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $pedido['itens'] = $itens;
        }

        return $pedido;
    }

    public function buscarTodos() {
        $stmt = $this->pdo->query("SELECT p.*, c.nome AS nome_cliente FROM pedidos p JOIN clientes c ON p.cliente_id = c.id");
        $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($pedidos as &$pedido) {
            $stmt = $this->pdo->prepare("SELECT pi.*, pr.nome AS nome_produto FROM pedido_itens pi JOIN produtos pr ON pi.produto_id = pr.id WHERE pi.pedido_id = ?");
            $stmt->execute([$pedido['id']]);
            $pedido['itens'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $pedidos;
    }

    public function deletar($pedido_id) {
        try {
            $this->pdo->beginTransaction();

            $stmt = $this->pdo->prepare("DELETE FROM pedido_itens WHERE pedido_id = ?");
            $stmt->execute([$pedido_id]);

            $stmt = $this->pdo->prepare("DELETE FROM pedidos WHERE id = ?");
            $stmt->execute([$pedido_id]);

            if ($stmt->rowCount() === 0) {
                $this->pdo->rollBack();
                return ["erro" => "Pedido não encontrado."];
            }

            $this->pdo->commit();
            return ["mensagem" => "Pedido deletado com sucesso!"];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ["erro" => $e->getMessage()];
        }
    }

    public function atualizar($pedido_id, $data) {
        try {
            $this->pdo->beginTransaction();

            $valoresValidos = ['pendente', 'processando', 'concluido', 'cancelado'];
            $status = $data['status_pedido'] ?? 'pendente';

            if (!in_array($status, $valoresValidos)) {
                throw new Exception("Status inválido: '$status'.");
            }

            $stmt = $this->pdo->prepare("UPDATE pedidos SET cliente_id = ?, status_pedido = ? WHERE id = ?");
            $stmt->execute([$data['cliente_id'], $status, $pedido_id]);

            $stmt = $this->pdo->prepare("DELETE FROM pedido_itens WHERE pedido_id = ?");
            $stmt->execute([$pedido_id]);

            $total = 0;
            foreach ($data['itens'] as $item) {
                $stmt = $this->pdo->prepare("SELECT preco FROM produtos WHERE id = ?");
                $stmt->execute([$item['produto_id']]);
                $produto = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$produto) {
                    throw new Exception("Produto ID {$item['produto_id']} não encontrado.");
                }

                $subtotal = $produto['preco'] * $item['quantidade'];
                $total += $subtotal;

                $stmt = $this->pdo->prepare("INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$pedido_id, $item['produto_id'], $item['quantidade'], $produto['preco'], $subtotal]);
            }

            $stmt = $this->pdo->prepare("UPDATE pedidos SET total = ? WHERE id = ?");
            $stmt->execute([$total, $pedido_id]);

            $this->pdo->commit();
            return ["mensagem" => "Pedido atualizado com sucesso"];
        } catch (Exception $e) {
            $this->pdo->rollBack();
            return ["erro" => $e->getMessage()];
        }
    }
}
