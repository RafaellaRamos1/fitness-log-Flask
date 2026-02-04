<?php
require_once 'config/database.php';
verificar_login(); 
include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h2 class="mb-4 text-warning"><i class="bi bi-plus-circle-dotted"></i> Novo Exercício</h2>
        
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="processa.php" method="POST">
                    <input type="hidden" name="acao" value="salvar_novo_tipo">
                    
                    <div class="mb-3">
                        <label for="nome" class="form-label fw-bold">Nome do Exercício</label>
                        <input type="text" class="form-control" name="nome" required placeholder="Ex: CrossFit, Zumba, Jiu-Jitsu...">
                    </div>
                    
                    <div class="mb-3">
                        <label for="calorias_por_minuto" class="form-label fw-bold">Calorias (Estimativa por min)</label>
                        <input type="number" class="form-control" name="calorias_por_minuto" required value="5">
                        <div class="form-text">Use uma média. Ex: Caminhada = 4, Corrida = 10.</div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="treinos_cadastrar.php" class="btn btn-secondary me-md-2">Cancelar</a>
                        <button type="submit" class="btn btn-warning">Salvar e Voltar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include 'includes/footer.php';
?>