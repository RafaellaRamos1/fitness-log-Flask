from flask import Blueprint, render_template, redirect, url_for, flash, request
from flask_login import login_user, logout_user, login_required, current_user
from werkzeug.security import check_password_hash
from .models import Usuario

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