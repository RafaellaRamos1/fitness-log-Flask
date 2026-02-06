from flask import Blueprint, render_template, redirect, url_for, flash, request
from flask_login import login_user, logout_user, login_required, current_user
from werkzeug.security import check_password_hash, generate_password_hash
from .models import Usuario, Treino, db, TipoExercicio
from datetime import datetime

# Arquitetura: Blueprints permitem dividir o app em módulos
main = Blueprint('main', __name__)

@main.route('/', methods=['GET', 'POST'])
def index():
    # Se já estiver logado, evita mostrar a tela de login de novo
    if current_user.is_authenticated:
        return redirect(url_for('main.dashboard'))

    # Lógica de processamento do formulário (Substitui o seu processa.php)
    if request.method == 'POST':
        email_form = request.form.get('email')
        senha_form = request.form.get('senha')

        # Busca no banco
        user = Usuario.query.filter_by(email=email_form).first()

        # Verifica senha
        if user and check_password_hash(user.senha, senha_form):
            login_user(user)
            return redirect(url_for('main.index'))
        else:
            flash('Login inválido! Verifique email e senha.')

    return render_template('index.html')

@main.route('/logout')
@login_required
def logout():
    logout_user()
    flash('Você saiu do sistema.')
    return redirect(url_for('main.index'))

@main.route('/dashboard')
@login_required
def dashboard():
    data_filtro = request.args.get('filtro_data')
    
    # Busca treinos do usuário logado
    query = Treino.query.filter_by(usuario_id=current_user.id)

    # Verifica se a data não é apenas uma string vazia
    if data_filtro and data_filtro.strip():
        query = query.filter(Treino.data == data_filtro)
    
    meus_treinos = query.order_by(Treino.data.desc()).all()
    
    print(f"DEBUG: Usuário {current_user.id} - Treinos encontrados: {len(meus_treinos)}")

    total_calorias = sum(t.duracao * t.tipo.calorias_por_minuto for t in meus_treinos)
    return render_template('dashboard.html', treinos=meus_treinos, total=total_calorias)
    
@main.route('/registrar', methods=['GET', 'POST'])
def registrar():
    if request.method == 'POST':
        nome = request.form.get('nome')
        user_login = request.form.get('usuario')
        email = request.form.get('email')
        senha = request.form.get('senha')

        # Verificação de usuário
        usuario_existente = Usuario.query.filter_by(usuario=user_login).first()
        email_existente = Usuario.query.filter_by(email=email).first()

        if usuario_existente:
            flash('Este nome de usuário já está em uso. Escolha outro.', 'danger')
            return redirect(url_for('main.registrar'))
        
        if email_existente:
            flash('Este e-mail já está cadastrado.', 'danger')
            return redirect(url_for('main.registrar'))

        # Arquitetura de Segurança: Criptografando a senha antes de salvar
        senha_hash = generate_password_hash(senha)

        novo_usuario = Usuario(nome=nome, usuario=user_login, email=email, senha=senha_hash, nivel='Comum')
        
        try:
            db.session.add(novo_usuario)
            db.session.commit() # Aqui o SQLAlchemy salva no MySQL
            flash("Usuário criado com sucesso!")
            return redirect(url_for('main.index'))
        except Exception as e:
            db.session.rollback()
            return f"Erro ao salvar: {str(e)}"

    return render_template('usuarios_cadastrar.html')

@main.route('/treino/novo', methods=['GET', 'POST'])
@login_required
def cadastrar_treino():
    tipos = TipoExercicio.query.all()
    
    if request.method == 'POST':
        tipo_id = request.form.get('tipo_exercicio_id') 
        duracao = request.form.get('duracao_minutos')
        data_str = request.form.get('data_treino')
        obs = request.form.get('observacoes')
        
        try:
            # Validação da Data
            data_objeto = datetime.strptime(data_str, '%Y-%m-%d')

            # Criação do objeto
            novo_treino = Treino(
                duracao=duracao,
                data=data_objeto,
                observacoes=obs,
                usuario_id=current_user.id,
                tipo_id=tipo_id
            )

            db.session.add(novo_treino)
            db.session.commit()
            
            flash('Treino registrado com sucesso!')
            return redirect(url_for('main.dashboard'))
            
        except Exception as e:
            db.session.rollback()
            # Se der erro, mostra erro e a página recarrega com a mensagem
            flash(f'Erro ao salvar treino: {str(e)}')
            # Aqui ele continua para o return final lá embaixo

    # Isso garante que a data atual apareça no formulário ao abrir (GET)
    data_hoje = datetime.now().strftime('%Y-%m-%d')
    return render_template('treinos_cadastrar.html', tipos=tipos, data_atual=data_hoje)

@main.route('/tipo-exercicio/novo', methods=['GET', 'POST'])
@login_required
def novo_tipo_exercicio():
    if request.method == 'POST':
        nome = request.form.get('nome')
        calorias = request.form.get('calorias_por_minuto')
        
        novo = TipoExercicio(descricao=nome, calorias_por_minuto=calorias)
        db.session.add(novo)
        db.session.commit()
        flash('Novo tipo de exercício adicionado!')
        return redirect(url_for('main.cadastrar_treino')) # Volta para a tela de treino
        
    return render_template('tipos_novo.html')

@main.route('/usuarios')
@login_required
def gerenciar_usuarios():
    # Proteção de Admin
    if current_user.nivel != 'Admin':
        flash('Acesso negado!')
        return redirect(url_for('main.index'))
        
    lista_usuarios = Usuario.query.order_by(Usuario.usuario.asc()).all()
    return render_template('usuarios_lista.html', usuarios=lista_usuarios)

@main.route('/usuarios/excluir/<int:id>')
@login_required
def excluir_usuario(id):
    if current_user.nivel != 'Admin' or current_user.id == id:
        flash('Ação não permitida!')
        return redirect(url_for('main.gerenciar_usuarios'))
    
    user = Usuario.query.get(id)
    if user:
        db.session.delete(user)
        db.session.commit()
        flash(f'Usuário {user.usuario} removido!')
    return redirect(url_for('main.gerenciar_usuarios'))

from werkzeug.security import generate_password_hash

@main.route('/usuarios/novo', methods=['GET', 'POST'])
@login_required
def cadastrar_usuario():
    if current_user.nivel != 'Admin':
        return redirect(url_for('main.index'))

    if request.method == 'POST':
        nome = request.form.get('nome')
        usuario = request.form.get('usuario')
        email = request.form.get('email')
        senha = request.form.get('senha')
        nivel = request.form.get('nivel', 'Comum')

        usuario_existente = Usuario.query.filter_by(usuario=usuario).first()
        if usuario_existente:
            flash(f'O login "{usuario}" já está em uso!', 'danger')
            return redirect(url_for('main.cadastrar_usuario'))

        # Criptografa a senha antes de salvar
        senha_hash = generate_password_hash(senha)
        
        novo_user = Usuario(nome=nome, usuario=usuario, email=email, senha=senha_hash, nivel=nivel)
        db.session.add(novo_user)
        db.session.commit()
        flash('Usuário cadastrado com sucesso!')
        return redirect(url_for('main.gerenciar_usuarios'))

    return render_template('usuarios_cadastrar.html')

@main.route('/usuarios/editar/<int:id>', methods=['GET', 'POST'])
@login_required
def editar_usuario(id):
    user = Usuario.query.get_or_404(id)
    is_self_edit = (user.id == current_user.id)

    if request.method == 'POST':
        # Valores do formulário
        novo_nome = request.form.get('nome')
        novo_usuario = request.form.get('usuario')
        novo_email = request.form.get('email')
        novo_nivel = request.form.get('nivel')
        nova_senha = request.form.get('senha')

        # SÓ ATUALIZA SE O CAMPO NÃO ESTIVER VAZIO
        if novo_nome:
            user.nome = novo_nome
        
        if novo_usuario:
            user.usuario = novo_usuario
            
        if novo_email:
            user.email = novo_email

        # Nível: Só muda se o Admin enviou e não é auto-edição
        if novo_nivel and not is_self_edit and current_user.nivel == 'Admin':
            user.nivel = novo_nivel
        
        # Senha: Só criptografa se digitaram algo
        if nova_senha:
            user.senha = generate_password_hash(nova_senha)

        try:
            db.session.commit()
            flash('Alterações salvas com sucesso!', 'success')
            return redirect(url_for('main.gerenciar_usuarios'))
        except Exception as e:
            db.session.rollback()
            flash(f'Erro ao salvar: {str(e)}', 'danger')

    return render_template('usuarios_editar.html', user_data=user, is_self_edit=is_self_edit)

@main.route('/treino/excluir/<int:id>')
@login_required
def excluir_treino(id):
    # Busca o treino ou retorna 404 se não existir
    treino = Treino.query.get_or_404(id)
    
    # Arquitetura de Segurança: Verifica se o treino pertence ao usuário logado
    if treino.usuario_id != current_user.id:
        flash('Você não tem permissão para excluir este treino!', 'danger')
        return redirect(url_for('main.dashboard'))
    
    try:
        db.session.delete(treino)
        db.session.commit()
        flash('Treino removido com sucesso!', 'success')
    except Exception as e:
        db.session.rollback()
        flash(f'Erro ao excluir: {str(e)}', 'danger')
        
    return redirect(url_for('main.dashboard'))

@main.route('/treino/editar/<int:id>', methods=['GET', 'POST'])
@login_required
def editar_treino(id):
    # Busca o treino ou retorna 404
    treino = Treino.query.get_or_404(id)
    
    # Segurança: Garante que o usuário só edite os próprios treinos 
    if treino.usuario_id != current_user.id:
        flash('Acesso negado!', 'danger')
        return redirect(url_for('main.dashboard'))

    # Busca tipos de exercício para o select do formulário 
    tipos = TipoExercicio.query.all()

    if request.method == 'POST':
        try:
            # Atualiza os campos com os dados do formulário 
            treino.tipo_id = request.form.get('tipo_exercicio_id')
            treino.duracao = request.form.get('duracao_minutos')
            
            # Converte a string de data do HTML para objeto date do Python 
            data_str = request.form.get('data_treino')
            treino.data = datetime.strptime(data_str, '%Y-%m-%d').date()
            
            treino.observacoes = request.form.get('observacoes')

            db.session.commit()
            flash('Treino atualizado com sucesso!', 'success')
            return redirect(url_for('main.dashboard'))
        except Exception as e:
            db.session.rollback()
            flash(f'Erro ao atualizar: {str(e)}', 'danger')

    return render_template('treinos_editar.html', treino=treino, tipos=tipos)
