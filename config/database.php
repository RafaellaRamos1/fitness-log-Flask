<?php
// Define as configurações de conexão
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fitness_db";

// Tenta estabelecer a conexão com o MySQL
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Verifica a conexão
if (!$conn) {
    // Exibe erro fatal em caso de falha de conexão com o banco
    die("Falha na conexão com o banco de dados: " . mysqli_connect_error());
}

// Inicia a sessão PHP (essencial para login e persistência de dados do utilizador)
session_start();

/**
* Função de segurança: Verifica se o utilizador está logado.
* Se não estiver, redireciona para a página de login (index.php) com erro.
*/
function verificar_login() {
    if (!isset($_SESSION['usuario_logado'])) {
        // Redireciona corretamente para a raiz do projeto (index.php)
        $caminho_base = (basename(getcwd()) == 'fitness_log') ? '' : '../';
        header("Location: " . $caminho_base . "index.php?erro=Acesso negado! Faça login para continuar.");
        exit();
    }
}
/**
* Função de segurança: Verifica se o utilizador logado é Administrador.
*/
function verificar_admin() {
    verificar_login(); // Garante que está logado primeiro
    if (!isset($_SESSION['usuario_nivel']) || $_SESSION['usuario_nivel'] != 'Admin') {
        // Redireciona com erro de permissão
        $caminho_base = (basename(getcwd()) == 'fitness_log') ? '' : '../';
        header("Location: " . $caminho_base . "index.php?erro=Acesso restrito a Administradores.");
        exit();
    }
}