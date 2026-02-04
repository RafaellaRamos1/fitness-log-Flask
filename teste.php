<?php
// Força o PHP a mostrar erros na tela
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h1>Testando Conexão...</h1>";

// Tenta incluir o seu arquivo de configuração
require_once 'config/database.php';

// Se chegou aqui, o arquivo foi carregado
echo "<p>Arquivo de configuração carregado.</p>";

// Testa se a variável $conn existe e é válida
if (isset($conn) && $conn) {
    echo "<h2 style='color:green'>SUCESSO: Conectado ao banco 'fitness_db'!</h2>";
} else {
    echo "<h2 style='color:red'>ERRO: A variável \$conn não foi criada.</h2>";
}
?>