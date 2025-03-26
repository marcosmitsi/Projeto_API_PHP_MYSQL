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

        public function buscarPorId($id) {
            $stmt = $this->pdo->prepare("SELECT * FROM pedidos WHERE id = ?");
            $stmt->execute([$id]);
            $pedido = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($pedido) {
                // Buscar itens do pedido
                $stmt = $this->pdo->prepare("SELECT * FROM pedido_itens WHERE pedido_id = ?");
                $stmt->execute([$id]);
                $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                $pedido['itens'] = $itens;
            }
    
            return $pedido;
        }
    
        public function buscarTodos() {
            $stmt = $this->pdo->query("SELECT * FROM pedidos");
            $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            foreach ($pedidos as &$pedido) {
                // Buscar itens de cada pedido
                $stmt = $this->pdo->prepare("SELECT * FROM pedido_itens WHERE pedido_id = ?");
                $stmt->execute([$pedido['id']]);
                $itens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                $pedido['itens'] = $itens;
            }
    
            return $pedidos;
        }

        public function deletar($pedido_id) {
            try {
                // Iniciar transação para garantir a integridade dos dados
                $this->pdo->beginTransaction();
        
                // Deletar os itens associados ao pedido
                $stmt = $this->pdo->prepare("DELETE FROM pedido_itens WHERE pedido_id = ?");
                $stmt->execute([$pedido_id]);
        
                // Deletar o pedido
                $stmt = $this->pdo->prepare("DELETE FROM pedidos WHERE id = ?");
                $stmt->execute([$pedido_id]);
        
                // Verificar se algum registro foi afetado
                if ($stmt->rowCount() === 0) {
                    // Nenhum registro foi deletado, desfazendo a transação
                    $this->pdo->rollBack();
                    return ["erro" => "Pedido não encontrado."];
                }
        
                // Confirmar a transação
                $this->pdo->commit();
        
                return ["mensagem" => "Pedido deletado com sucesso!"];
            } catch (Exception $e) {
                // Reverter a transação em caso de erro
                $this->pdo->rollBack();
                return ["erro" => $e->getMessage()];
            }
        }

        public function atualizar($pedido_id, $data) {
            try {
                $this->pdo->beginTransaction();
        
                // Atualizar informações do pedido
                $stmt = $this->pdo->prepare("UPDATE pedidos SET cliente_id = ? WHERE id = ?");
                $stmt->execute([$data['cliente_id'], $pedido_id]);
        
                // Remover itens antigos do pedido
                $stmt = $this->pdo->prepare("DELETE FROM pedido_itens WHERE pedido_id = ?");
                $stmt->execute([$pedido_id]);
        
                // Inserir novos itens do pedido
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
        
                // Atualizar o total do pedido
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

