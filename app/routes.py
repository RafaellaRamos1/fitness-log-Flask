from flask import Blueprint, render_template, redirect, url_for, flash, request
from flask_login import login_user, logout_user, login_required, current_user
from werkzeug.security import check_password_hash, generate_password_hash
from .models import Usuario, Treino, db

# Arquitetura: Blueprints permitem dividir o app em módulos
main = Blueprint('main', __name__)

@main.route('/', methods=['GET', 'POST'])
def index():
    # Se já estiver logado, evita mostrar a tela de login de novo
    if current_user.is_authenticated:
        return render_template('dashboard.html') # Vamos criar esse arquivo rapidinho depois

    # Lógica de processamento do formulário (Substitui o seu processa.php)
    if request.method == 'POST':
        usuario_form = request.form.get('usuario')
        senha_form = request.form.get('senha')

        # Busca no banco
        user = Usuario.query.filter_by(usuario=usuario_form).first()

        # Verifica senha. OBS: Como seu banco antigo usa PHP password_hash,
        # o check_password_hash do Python geralmente consegue ler se for Bcrypt.
        if user and check_password_hash(user.senha, senha_form):
            login_user(user)
            return redirect(url_for('main.index'))
        else:
            flash('Login inválido! Verifique usuário e senha.')

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
    # O ORM busca no banco usando a classe que você criou
    meus_treinos = Treino.query.filter_by(usuario_id=current_user.id).all()
    return render_template('dashboard.html', treinos=meus_treinos)

@main.route('/registrar', methods=['GET', 'POST'])
def registrar():
    if request.method == 'POST':
        nome = request.form.get('nome')
        user_login = request.form.get('usuario')
        senha = request.form.get('senha')

        # Arquitetura de Segurança: Criptografando a senha antes de salvar
        senha_hash = generate_password_hash(senha)

        novo_usuario = Usuario(nome=nome, usuario=user_login, senha=senha_hash)
        
        try:
            db.session.add(novo_usuario)
            db.session.commit() # Aqui o SQLAlchemy salva no MySQL
            flash("Usuário criado com sucesso!")
            return redirect(url_for('main.index'))
        except Exception as e:
            db.session.rollback()
            return f"Erro ao salvar: {str(e)}"

    return render_template('usuarios_cadastrar.html')
