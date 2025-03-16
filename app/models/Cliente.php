<?php
require_once __DIR__ . '/../../config/database.php';

class Cliente {
    private $pdo;

    public function __construct() {
        $this->pdo = getConnection(); // Agora chamamos a função correta!
    }

    public function listar() {
        $stmt = $this->pdo->query("SELECT * FROM clientes");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM clientes WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function criar($data) {
        // Verificar se o email já existe
        $stmt = $this->pdo->prepare("SELECT id FROM clientes WHERE email = ?");
        $stmt->execute([$data['email']]);
        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(["erro" => "O e-mail já está cadastrado."]);
            exit; // Interrompe a execução para evitar mensagens duplicadas
        }
    
        // Inserir novo cliente
        $stmt = $this->pdo->prepare("INSERT INTO clientes (nome, email, telefone, endereco) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$data['nome'], $data['email'], $data['telefone'], $data['endereco']]);
    }

    public function atualizar($id, $data) {
        // Verificar se o email já pertence a outro cliente
        $stmt = $this->pdo->prepare("SELECT id FROM clientes WHERE email = ? AND id != ?");
        $stmt->execute([$data['email'], $id]);
        
        if ($stmt->fetch()) {
            http_response_code(400);
            echo json_encode(["erro" => "O e-mail já está cadastrado para outro cliente."]);
            exit;
        }
    
        // Atualizar os dados do cliente
        $stmt = $this->pdo->prepare("UPDATE clientes SET nome = ?, email = ?, telefone = ?, endereco = ? WHERE id = ?");
        $stmt->execute([$data['nome'], $data['email'], $data['telefone'], $data['endereco'], $id]);
    
    }
    
    

    public function deletar($id) {
        $stmt = $this->pdo->prepare("DELETE FROM clientes WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

