<?php
include 'includes/header.php'; 
?>

<div class="row justify-content-center mt-5">
    <div class="col-md-5">
        <div class="card shadow-lg border-0">
            <div class="card-body p-5">
                <h2 class="text-center mb-4 fw-bold text-warning">Redefinir Senha</h2>
                <p class="text-center text-muted mb-4">Digite seu nome de usuário e a nova senha.</p>
                
                <?php 
                $mensagem_erro = isset($_GET['erro']) ? htmlspecialchars($_GET['erro']) : '';
                if (!empty($mensagem_erro)): ?>
                    <div class="alert alert-danger"><?= $mensagem_erro ?></div>
                <?php endif; ?>

                <form action="processa.php" method="POST">
                    <input type="hidden" name="acao" value="redefinir_senha">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nome de Usuário</label>
                        <input type="text" name="usuario" class="form-control form-control-lg" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nova Senha</label>
                        <input type="password" name="nova_senha" class="form-control form-control-lg" required minlength="6">
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                        <a href="index.php" class="btn btn-secondary me-md-2">Cancelar</a>
                        <button type="submit" class="btn btn-warning me-md-2">REDEFINIR SENHA</button>                        
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
include 'includes/footer.php';
?>

