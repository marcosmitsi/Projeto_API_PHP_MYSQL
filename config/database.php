<?php
// Configurações do banco de dados
define('DB_HOST', 'localhost'); 
define('DB_NAME', 'kidelicia');
define('DB_USER', 'root');  // Usuário do MySQL
define('DB_PASS', '243156');      // Senha do MySQL (deixe vazio se não tiver)

// Função para conectar ao banco de dados
function getConnection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8", DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Erro na conexão: " . $e->getMessage());
    }
}

