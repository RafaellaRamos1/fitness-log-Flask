<?php
require_once 'config/database.php';
verificar_login(); // Proteção: só quem está logado pode ver esta página

// --- BUSCAR TIPOS DE EXERCÍCIO ---
// Busca os exercícios públicos (NULL) OU os criados pelo próprio usuário logado
$id_usuario = $_SESSION['usuario_id'];
$sql_tipos = "SELECT * FROM tipos_exercicio 
              WHERE criado_por IS NULL OR criado_por = $id_usuario 
              ORDER BY descricao ASC";
$result_tipos = mysqli_query($conn, $sql_tipos);

include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <h1 class="mb-4 text-warning"><i class="bi bi-stopwatch"></i> Registrar Novo Treino</h1>

        <?php if (isset($_GET['erro'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_GET['erro']); ?>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="processa.php" method="POST">
                    <input type="hidden" name="acao" value="cadastrar_treino">

                    <div class="mb-3">
                        <label for="tipo_exercicio_id" class="form-label fw-bold">Tipo de Atividade</label>
                        <div class="input-group">
                            <select class="form-select" name="tipo_exercicio_id" id="tipo_exercicio_id" required>
                                <option value="">Selecione o exercício...</option>
                                <?php while ($tipo = mysqli_fetch_assoc($result_tipos)): ?>
                                    <option value="<?= $tipo['id']; ?>">
                                        <?= htmlspecialchars($tipo['descricao']); ?> 
                                        (aprox. <?= $tipo['calorias_por_minuto']; ?> cal/min)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                            <a href="tipos_novo.php" class="btn btn-outline-secondary" title="Criar novo tipo personalizado">
                                <i class="bi bi-plus-lg"></i> Novo
                            </a>
                        </div>
                        <div class="form-text">Não achou seu exercício? Clique em "Novo" para criar.</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="duracao_minutos" class="form-label fw-bold">Duração (minutos)</label>
                            <input type="number" class="form-control" id="duracao_minutos" name="duracao_minutos" required min="1" placeholder="Ex: 45">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="data_treino" class="form-label fw-bold">Data do Treino</label>
                            <input type="date" class="form-control" id="data_treino" name="data_treino" required value="<?= date('Y-m-d'); ?>">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="observacoes" class="form-label fw-bold">Observações (Opcional)</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="3" placeholder="Como se sentiu? Cargas usadas? Distância?"></textarea>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="index.php" class="btn btn-secondary me-md-2">Cancelar</a>
                        <button type="submit" class="btn btn-warning btn-lg px-5">Salvar Treino</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>