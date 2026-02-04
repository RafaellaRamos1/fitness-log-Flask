<?php
//session_start();
// --- MODO DE DEPURAÇÃO (Pode remover quando estiver tudo pronto) ---
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// -----------------------------------------------------------------

// 1. Inclui a configuração do banco de dados
require_once 'config/database.php';

// Ativa relatórios de erro do MySQL para ajudar a identificar falhas de SQL
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// 2. Recebe a ação enviada pelo formulário
$acao = isset($_REQUEST['acao']) ? $_REQUEST['acao'] : '';

try {
    switch ($acao) {
        // ==================================================
        // LOGICA DE LOGIN
        // ==================================================
        case 'login':
            $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_SPECIAL_CHARS);
            $senha = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS);

            $stmt = mysqli_prepare($conn, "SELECT id, usuario, senha, nivel FROM usuarios WHERE usuario = ?");
            mysqli_stmt_bind_param($stmt, "s", $usuario);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);

            if ($linha = mysqli_fetch_assoc($resultado)) {
                if (password_verify($senha, $linha['senha'])) {
                    $_SESSION['usuario_logado'] = $linha['usuario'];
                    $_SESSION['usuario_id'] = $linha['id'];
                    $_SESSION['usuario_nivel'] = $linha['nivel'];
                    header("Location: index.php?msg=Bem-vindo(a), " . $linha['usuario'] . "!");
                    exit();
                }
            }
            header("Location: index.php?erro=Credenciais inválidas.");
            break;

        // ==================================================
        // LOGICA DE LOGOUT
        // ==================================================
        case 'logout':
            session_unset();
            session_destroy();
            header("Location: index.php?msg=Você saiu do sistema.");
            exit();

        // ==================================================
        // CADASTRAR TREINO (Corrigido)
        // ==================================================
        case 'cadastrar_treino':
            verificar_login();

            // Recebe dados
            $tipo_id = filter_input(INPUT_POST, 'tipo_exercicio_id', FILTER_VALIDATE_INT);
            $duracao = filter_input(INPUT_POST, 'duracao_minutos', FILTER_VALIDATE_INT);
            $data = filter_input(INPUT_POST, 'data_treino', FILTER_SANITIZE_SPECIAL_CHARS);
            $obs = filter_input(INPUT_POST, 'observacoes', FILTER_SANITIZE_SPECIAL_CHARS); 
            $usuario_id = $_SESSION['usuario_id'];

            if (!$tipo_id || !$duracao || !$data) {
                throw new Exception("Preencha todos os campos obrigatórios.");
            }

            $sql = "INSERT INTO treinos (usuario_id, tipo_exercicio_id, duracao_minutos, data_treino, observacoes) VALUES (?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iiiss", $usuario_id, $tipo_id, $duracao, $data, $obs);
            mysqli_stmt_execute($stmt);

            header("Location: index.php?msg=Treino registrado com sucesso!");
            break;
    
        // ==================================================
        // SALVAR NOVO TIPO (Corrigido)
        // ==================================================
        case 'salvar_novo_tipo':
            verificar_login(); 

            $nome_exercicio = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS);
            $calorias = filter_input(INPUT_POST, 'calorias_por_minuto', FILTER_VALIDATE_INT);
            $usuario_id = $_SESSION['usuario_id'];

            if (empty($nome_exercicio)) {
                throw new Exception("O nome do exercício é obrigatório.");
            }

            $sql = "INSERT INTO tipos_exercicio (descricao, calorias_por_minuto, criado_por) VALUES (?, ?, ?)";
            
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sii", $nome_exercicio, $calorias, $usuario_id);
            mysqli_stmt_execute($stmt);

            header("Location: treinos_cadastrar.php?msg=Novo tipo criado com sucesso!");
            break;

        // ==================================================
        // EXCLUIR TREINO 
        // ==================================================
        case 'excluir_treino':
            verificar_login();  

            $id_treino = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            $usuario_id = $_SESSION['usuario_id'];

            if (!$id_treino) {
                header("Location: index.php?erro=ID inválido.");
                exit();
            }

            $stmt = mysqli_prepare($conn, "DELETE FROM treinos WHERE id = ? AND usuario_id = ?");
            mysqli_stmt_bind_param($stmt, "ii", $id_treino, $usuario_id);
            mysqli_stmt_execute($stmt);

            header("Location: index.php?msg=Treino excluído.");
            break;

        // ==================================================
        // EDITAR TREINO (Corrigido)
        // ==================================================
        case 'editar_treino':
            verificar_login();

            $id_treino = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT); 
            $tipo_id = filter_input(INPUT_POST, 'tipo_exercicio_id', FILTER_VALIDATE_INT);
            $duracao = filter_input(INPUT_POST, 'duracao_minutos', FILTER_VALIDATE_INT);
            $data = filter_input(INPUT_POST, 'data_treino', FILTER_SANITIZE_SPECIAL_CHARS);
            $obs = filter_input(INPUT_POST, 'observacoes', FILTER_SANITIZE_SPECIAL_CHARS);
            $usuario_id = $_SESSION['usuario_id'];

            $sql = "UPDATE treinos SET tipo_exercicio_id=?, duracao_minutos=?, data_treino=?, observacoes=? WHERE id=? AND usuario_id=?";
            
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "iisssi", $tipo_id, $duracao, $data, $obs, $id_treino, $usuario_id);
            mysqli_stmt_execute($stmt);

            header("Location: index.php?msg=Treino atualizado!");
            break;
        
        // ==================================================
        // CADASTRAR USUÁRIO 
        // ==================================================
        case 'cadastrar_usuario':
            //verificar_admin(); // Só admin cadastra

            $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_SPECIAL_CHARS);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
            $senha_original = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS);
            //$nivel = filter_input(INPUT_POST, 'nivel', FILTER_SANITIZE_SPECIAL_CHARS);
            $nivel = 'Comum'; 
            // Criptografa a senha antes de salvar (OBRIGATÓRIO)
            $senha_hash = password_hash($senha_original, PASSWORD_DEFAULT);

            // Tenta inserir
            $sql = "INSERT INTO usuarios (usuario, email, senha, nivel) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "ssss", $usuario, $email, $senha_hash, $nivel);

            try {
                if (mysqli_stmt_execute($stmt)) {
                    header("Location: index.php?msg=Cadastro feito.");
                    exit();
                }
            } catch (mysqli_sql_exception $e) {
                // Erro 1062 significa duplicidade (usuário já existe)
                if ($e->getCode() == 1062) {
                    header("Location: usuarios_cadastrar.php?erro=Erro: Este nome de usuário já existe.");
                } else {
                    throw $e; // Outro erro
                }
            }
            break;

        // ==================================================
        // EDITAR USUÁRIO 
        // ==================================================
        case 'editar_usuario':
            //verificar_admin(); // Só admin cadastra

            $id_usuario = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
                $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_SPECIAL_CHARS);
                $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
                $senha_original = filter_input(INPUT_POST, 'senha', FILTER_SANITIZE_SPECIAL_CHARS); // Pode ser vazio
                $nivel = filter_input(INPUT_POST, 'nivel', FILTER_SANITIZE_SPECIAL_CHARS);

                if (!$id_usuario || empty($usuario) || empty($email) || empty($nivel)) {
                    header("Location: usuario_editar.php?id=" . $id_usuario . "&erro=Erro: Campos obrigatórios faltando.");
                    exit();
                }

                $sql = "UPDATE usuarios SET usuario = ?, email = ?, nivel = ?";
                $params = [$usuario, $email, $nivel];
                $tipos = "sss";
                
                // Se a senha foi fornecida, atualiza a senha também
                if (!empty($senha_original)) {
                    $senha_hash = password_hash($senha_original, PASSWORD_DEFAULT);
                    $sql .= ", senha = ?";
                    $tipos .= "s";
                    $params[] = $senha_hash; // Adiciona o hash da senha aos parâmetros
                }

                $sql .= " WHERE id = ?";
                $tipos .= "i";
                $params[] = $id_usuario; // Adiciona o ID ao final dos parâmetros

                // Prepara e executa o statement
                $stmt = mysqli_prepare($conn, $sql);

                // Cria o array de referência para mysqli_stmt_bind_param
                // Isso é necessário pois bind_param exige que as variáveis sejam passadas por referência
                array_unshift($params, $tipos); // Adiciona a string de tipos no início
                call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt], $params));

                try {
                    if (mysqli_stmt_execute($stmt)) {
                        header("Location: usuarios.php?msg=Usuário **" . $usuario . "** editado com sucesso.");
                        exit();
                    }
                } catch (mysqli_sql_exception $e) {
                    // Erro 1062 significa duplicidade (usuário já existe)
                    if ($e->getCode() == 1062) {
                        header("Location: usuario_editar.php?id=" . $id_usuario . "&erro=Erro: Este nome de usuário ou email já existe.");
                    } else {
                        throw $e; // Outro erro
                    }
                }
                break;

        // ==================================================
        // EXCLUIR USUÁRIO 
        // ==================================================
        case 'excluir_usuario':
            verificar_admin();

            $id_excluir = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

            // Proteção: Não deixa excluir a si mesmo
            if ($id_excluir == $_SESSION['usuario_id']) {
                header("Location: usuarios.php?erro=Você não pode se excluir!");
                exit();
            }

            $stmt = mysqli_prepare($conn, "DELETE FROM usuarios WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $id_excluir);
            mysqli_stmt_execute($stmt);

            header("Location: usuarios.php?msg=Usuário excluído.");
            break;
        default:
            header("Location: index.php?erro=Ação inválida.");
        
        // ==================================================
        // REDEFINIR SENHA 
        // ==================================================

        case 'redefinir_senha':
            $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_SPECIAL_CHARS);
            $nova_senha = filter_input(INPUT_POST, 'nova_senha', FILTER_DEFAULT);

            if (empty($usuario) || empty($nova_senha)) {
                header("Location: redefinir_direto.php?erro=Preencha todos os campos.");
                exit();
            }

            // 1. Busca o ID do usuário
            $stmt = mysqli_prepare($conn, "SELECT id FROM usuarios WHERE usuario = ?");
            mysqli_stmt_bind_param($stmt, "s", $usuario);
            mysqli_stmt_execute($stmt);
            $resultado = mysqli_stmt_get_result($stmt);

            if ($linha = mysqli_fetch_assoc($resultado)) {
                $user_id = $linha['id'];
                
                // 2. Gera hash da nova senha
                $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

                // 3. Atualiza a senha no BD
                $update_stmt = mysqli_prepare($conn, "UPDATE usuarios SET senha = ? WHERE id = ?");
                mysqli_stmt_bind_param($update_stmt, "si", $nova_senha_hash, $user_id);
                mysqli_stmt_execute($update_stmt);

                header("Location: index.php?msg=Faça login com a nova senha.");
                exit();
            } else {
                // Usuário não encontrado
                header("Location: redefinir_senha.php?erro=Usuário não encontrado.");
                exit();
            }
            break;
    }

} catch (Exception $e) {
    die("<h1>Erro no Processamento:</h1><p>" . $e->getMessage() . "</p><p><a href='index.php'>Voltar</a></p>");
}

mysqli_close($conn);
?>