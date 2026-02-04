<?php
// Inclui a configuração do banco e a sessão
require_once 'config/database.php';
// Inclui o cabeçalho (início do HTML, navbar)
include 'includes/header.php';

// Variáveis para mensagens (sucesso ou erro)
$mensagem_sucesso = isset($_GET['msg']) ? htmlspecialchars($_GET['msg']) : '';
$mensagem_erro = isset($_GET['erro']) ? htmlspecialchars($_GET['erro']) : '';

// 1. EXIBIÇÃO DE MENSAGENS
if (!empty($mensagem_sucesso)):
?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <strong>Sucesso!</strong> <?= $mensagem_sucesso ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php
endif;

if (!empty($mensagem_erro)):
?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Erro!</strong> <?= $mensagem_erro ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div> 
<?php
endif;

// 2. LÓGICA CONDICIONAL: SE LOGADO OU NÃO
if (isset($_SESSION['usuario_logado'])):
    // =======================================================
    // ÁREA RESTRITA: UTILIZADOR LOGADO
    // =======================================================
    
    $id_usuario = $_SESSION['usuario_id'];
    $filtro_data = filter_input(INPUT_GET, 'filtro_data', FILTER_SANITIZE_SPECIAL_CHARS);

    // Começo da query base
    $sql = "SELECT t.*, te.descricao as nome_exercicio, te.calorias_por_minuto
            FROM treinos t 
            INNER JOIN tipos_exercicio te ON t.tipo_exercicio_id = te.id
            WHERE t.usuario_id = ?";

    if ($filtro_data) {
        $sql .= " AND t.data_treino = ?";
    }

    $sql .= " ORDER BY t.data_treino DESC";

    $stmt = mysqli_prepare($conn, $sql);

    if ($filtro_data) {
        mysqli_stmt_bind_param($stmt, "is", $id_usuario, $filtro_data);
    } else {
        mysqli_stmt_bind_param($stmt, "i", $id_usuario);
    }

    mysqli_stmt_execute($stmt);
    $resultado = mysqli_stmt_get_result($stmt);
    $total_calorias_acumulado = 0;
    $total_minutos_acumulado = 0;
?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Meu Diário de Treinos</h2>
        <a href="treinos_cadastrar.php" class="btn btn-warning btn-lg shadow-sm">
            <i class="bi bi-plus-circle"></i> Registrar Treino
        </a>
    </div>

    <div class="card mb-4 bg-light border-0">
        <div class="card-body py-3">
            <form action="index.php" method="GET" class="row g-3 align-items-end">
                <div class="col-auto">
                    <label class="form-label fw-bold mb-0 text-secondary">Filtrar por dia:</label>
                    <input type="date" name="filtro_data" class="form-control" value="<?= $filtro_data ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                    <?php if ($filtro_data): ?>
                        <a href="index.php" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Limpar
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <?php if (mysqli_num_rows($resultado) > 0): ?>
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-warning text-white">
                        <tr>
                            <th>Data</th>
                            <th>Exercício</th>
                            <th>Duração</th>
                            <th>Calorias Estimadas</th> <th>Obs</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($treino = mysqli_fetch_assoc($resultado)): 
                            // --- CÁLCULO MATEMÁTICO ---
                            // Multiplica minutos * calorias do tipo de exercício
                            $calorias_treino = $treino['duracao_minutos'] * $treino['calorias_por_minuto'];
                            
                            // Adiciona ao total geral
                            $total_calorias_acumulado += $calorias_treino;
                            $total_minutos_acumulado += $treino['duracao_minutos'];
                        ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($treino['data_treino'])); ?></td>
                                <td class="fw-bold text-warning">
                                    <?= htmlspecialchars($treino['nome_exercicio']); ?>
                                </td>
                                <td><?= $treino['duracao_minutos']; ?> min</td>
                                
                                <td class="fw-bold text-danger">
                                    <i class="bi bi-fire"></i> <?= number_format($calorias_treino, 0, ',', '.'); ?> kcal
                                </td>
                                
                                <td class="text-muted small"><?= htmlspecialchars($treino['observacoes']); ?></td>
                                <td>
                                    <a href="treinos_editar.php?id=<?= $treino['id']; ?>" class="btn btn-sm btn-outline-warning" title="Editar"><i class="bi bi-pencil"></i></a>
                                    <a href="processa.php?acao=excluir_treino&id=<?= $treino['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Excluir este treino?');" title="Excluir"><i class="bi bi-trash"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                    
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="2" class="text-end text-uppercase">Totais:</td>
                            <td class="text-warning"><?= $total_minutos_acumulado; ?> min</td>
                            <td class="text-danger fs-5"><?= number_format($total_calorias_acumulado, 0, ',', '.'); ?> kcal</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                    
                </table>
            </div>
        </div>
        
        <?php if ($filtro_data): ?>
            <div class="mt-2 text-end text-muted small">
                Resultados do dia: <?= date('d/m/Y', strtotime($filtro_data)); ?>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <div class="text-center py-5 text-muted">
            <i class="bi bi-activity display-1 text-light mb-3"></i>
            <?php if ($filtro_data): ?>
                <h4>Nenhum treino neste dia.</h4>
                <a href="index.php" class="btn btn-link">Ver todos</a>
            <?php else: ?>
                <h4>Comece registrando seu primeiro treino!</h4>
            <?php endif; ?>
        </div>
    <?php endif; ?>

<?php else: ?>

    <div class="row justify-content-center mt-5">
        <div class="col-md-5">
            <div class="card shadow-lg border-0">
                <div class="card-body p-5">
                    <h2 class="text-center mb-4 fw-bold text-warning">Fitness Log</h2>
                    <p class="text-center text-muted mb-4">Entre para acessar seus treinos</p>
                    <form action="processa.php" method="POST">
                        <input type="hidden" name="acao" value="login">
                        <div class="mb-3">
                            <label class="form-label">Usuário</label>
                            <input type="text" name="usuario" class="form-control form-control-lg" required placeholder="Ex: admin">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Senha</label>
                            <input type="password" name="senha" class="form-control form-control-lg" required placeholder="Ex: 123456">
                        </div>
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-warning btn-lg">ENTRAR</button>
                        </div>
                    </form>
                    <hr class="my-4">
                    <div class="text-center">
                        <a href="usuarios_cadastrar.php" class="btn btn-sm btn-outline-secondary w-100 mb-3">Criar Nova Conta</a>
                        <a href="redefinir_senha.php" class="btn btn-sm btn-link text-warning">Esqueci minha senha</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>

<?php
include 'includes/footer.php';
mysqli_close($conn);
?>