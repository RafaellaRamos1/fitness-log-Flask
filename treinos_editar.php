<?php
require_once 'config/database.php';
verificar_login(); // Proteção

// 1. Recebe o ID da URL (ex: treinos_editar.php?id=5)
$id_treino = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

// Se não tiver ID, chuta de volta para o início
if (!$id_treino) {
    header("Location: index.php?erro=ID inválido para edição.");
    exit();
}

// 2. Busca os dados desse treino específico no banco
// (Adicionamos "AND usuario_id = ..." para garantir que ninguém edite o treino de outro)
$usuario_id = $_SESSION['usuario_id'];
$stmt = mysqli_prepare($conn, "SELECT * FROM treinos WHERE id = ? AND usuario_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $id_treino, $usuario_id);
mysqli_stmt_execute($stmt);
$resultado_treino = mysqli_stmt_get_result($stmt);

// Se não achou o treino (ou não pertence a esse usuário)
if (mysqli_num_rows($resultado_treino) == 0) {
    header("Location: index.php?erro=Treino não encontrado.");
    exit();
}
$treino = mysqli_fetch_assoc($resultado_treino);

// 3. Busca a lista de exercícios para preencher o <select>
// (Mesma lógica do cadastro: públicos + criados pelo usuário)
$sql_tipos = "SELECT * FROM tipos_exercicio 
              WHERE criado_por IS NULL OR criado_por = $usuario_id 
              ORDER BY descricao ASC";
$result_tipos = mysqli_query($conn, $sql_tipos);

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <h2 class="mb-4 text-warning"><i class="bi bi-pencil-square"></i> Editar Treino</h2>

        <div class="card shadow-sm border-warning">
            <div class="card-body p-4">
                <form action="processa.php" method="POST">
                    <input type="hidden" name="acao" value="editar_treino">
                    <input type="hidden" name="id" value="<?= $treino['id']; ?>">

                    <div class="mb-3">
                        <label for="tipo_exercicio_id" class="form-label fw-bold">Tipo de Atividade</label>
                        <select class="form-select" name="tipo_exercicio_id" required>
                            <option value="">Selecione...</option>
                            <?php while ($tipo = mysqli_fetch_assoc($result_tipos)): ?>
                                <?php $selecionado = ($tipo['id'] == $treino['tipo_exercicio_id']) ? 'selected' : ''; ?>
                                
                                <option value="<?= $tipo['id']; ?>" <?= $selecionado; ?>>
                                    <?= htmlspecialchars($tipo['descricao']); ?> 
                                    (aprox. <?= $tipo['calorias_por_minuto']; ?> cal/min)
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="duracao_minutos" class="form-label fw-bold">Duração (minutos)</label>
                            <input type="number" class="form-control" name="duracao_minutos" 
                                   value="<?= $treino['duracao_minutos']; ?>" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="data_treino" class="form-label fw-bold">Data do Treino</label>
                            <input type="date" class="form-control" name="data_treino" 
                                   value="<?= $treino['data_treino']; ?>" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="observacoes" class="form-label fw-bold">Observações</label>
                        <textarea class="form-control" name="observacoes" rows="3"><?= htmlspecialchars($treino['observacoes']); ?></textarea>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="index.php" class="btn btn-secondary me-md-2">Cancelar</a>
                        <button type="submit" class="btn btn-warning px-4">Atualizar Treino</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>