<?php
session_start();
// Define o caminho base para que os links funcionem de qualquer pasta
$caminho = (basename(getcwd()) == 'fitness_log') ? '' : '../';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fitness Log | Registro de Treinos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f0f2f5; }
        .card-login { max-width: 450px; margin-top: 50px; }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-warning shadow-sm"> 
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-activity"></i> Fitness Log
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <?php if (isset($_SESSION['usuario_logado'])): ?>
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">Meus Treinos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url_for('main.cadastrar_treino') }}">Cadastrar Exercício</a>
                        </li>
                        <?php if (isset($_SESSION['usuario_nivel']) && $_SESSION['usuario_nivel'] == 'Admin'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="usuarios.php">Gerenciar Usuários</a> <!-- Admin only -->
                            </li>
                        <?php endif; ?>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['usuario_logado'])): ?>
                        <li class="nav-item me-3">
                            <span class="nav-link text-light">Olá, <strong><?php echo htmlspecialchars($_SESSION['usuario_logado']); ?></strong></span>
                        </li>
                        <li class="nav-item">
                            <a href="processa.php?acao=logout" class="btn btn-sm btn-danger mt-1">Sair</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container mt-4">

    
