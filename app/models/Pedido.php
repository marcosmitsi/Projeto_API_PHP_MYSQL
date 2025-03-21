<?php
require_once __DIR__ . '/../../config/database.php';

class Pedido {
    private $pdo;

    public function __construct() {
        $this->pdo = getConnection();
    }

    public function criar($data) {
   
            try {
                // Iniciar transação para garantir integridade dos dados
                $this->pdo->beginTransaction();
        
                // Criar o pedido
                $stmt = $this->pdo->prepare("INSERT INTO pedidos (cliente_id, total) VALUES (?, ?)");
                $stmt->execute([$data['cliente_id'], 0]); // O total será atualizado depois
                
                // Obter o ID do pedido criado
                $pedido_id = $this->pdo->lastInsertId();
        
                // Inserir os itens do pedido
                $total = 0;
                if (isset($data['itens']) && is_array($data['itens']) && !empty($data['itens'])) {
                    foreach ($data['itens'] as $item) {
                        if (!is_array($item) || !isset($item['produto_id']) || !isset($item['quantidade'])) {
                            throw new Exception("Formato de item inválido.");
                        }
                        // Buscar o preço do produto
                        $stmt = $this->pdo->prepare("SELECT preco FROM produtos WHERE id = ?");
                        $stmt->execute([$item['produto_id']]);
                        $produto = $stmt->fetch(PDO::FETCH_ASSOC);
        
                        if (!$produto) {
                            throw new Exception("Produto ID {$item['produto_id']} não encontrado.");
                        }
        
                        $subtotal = $produto['preco'] * $item['quantidade'];
                        $total += $subtotal;
        
                        // Inserir o item no pedido
                        $stmt = $this->pdo->prepare("INSERT INTO pedido_itens (pedido_id, produto_id, quantidade, preco_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
                        $stmt->execute([$pedido_id, $item['produto_id'], $item['quantidade'], $produto['preco'], $subtotal]);
                    }
                } else {
                    throw new Exception("Nenhum item encontrado no pedido.");
                }
        
                // Atualizar o total do pedido
                $stmt = $this->pdo->prepare("UPDATE pedidos SET total = ? WHERE id = ?");
                $stmt->execute([$total, $pedido_id]);
        
                // Confirmar transação
                $this->pdo->commit();
        
                return ["mensagem" => "Pedido criado com sucesso!", "pedido_id" => $pedido_id];
        
            } catch (Exception $e) {
                // Reverter transação em caso de erro
                $this->pdo->rollBack();
                return ["erro" => $e->getMessage()];
            }
        }
    }    

