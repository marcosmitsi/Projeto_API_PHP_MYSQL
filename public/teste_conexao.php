<?php
require_once __DIR__ . "/../config/database.php";

// Usando a função getConnection para estabelecer a conexão
$conn = getConnection();

if ($conn) {
    echo "✅ Conexão com o banco de dados bem-sucedida!";
} else {
    echo "❌ Falha na conexão!";
}
