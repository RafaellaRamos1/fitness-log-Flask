<?php
require_once 'config/database.php';
//verificar_admin(); //Apenas Admin
include 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h2 class="mb-4 text-warning"><i class="bi bi-person-plus"></i> Novo Usuário</h2>

        <?php if (isset($_GET['erro'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_GET['erro']); ?></div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form action="processa.php" method="POST">
                    <input type="hidden" name="acao" value="cadastrar_usuario">

                    <div class="mb-3">
                        <label class="form-label fw-bold">Login (Nome de Usuário)</label>
                        <input type="text" name="usuario" class="form-control" required placeholder="Ex: joao">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" required placeholder="Ex: joao@email.com">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Senha</label>
                        <input type="password" name="senha" class="form-control" required minlength="6">
                        <div class="form-text">A senha será criptografada no banco.</div>
                    </div>

                    <!---<div class="mb-3">
                        <label class="form-label fw-bold">Nível de Acesso</label>
                        <select name="nivel" class="form-select" required>
                            <option value="Comum" selected>Comum (Só vê os próprios treinos)</option>
                            <option value="Admin">Admin (Gerencia tudo)</option>
                        </select>
                    </div>--->

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <!---<a href="usuarios.php" class="btn btn-secondary me-md-2">Cancelar</a>--->
                        <a href="index.php" class="btn btn-secondary me-md-2">Cancelar</a>
                        <button type="submit" class="btn btn-warning">Cadastrar Usuário</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>