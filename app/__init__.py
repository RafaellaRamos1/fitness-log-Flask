from flask import Flask
from flask_sqlalchemy import SQLAlchemy
from flask_login import LoginManager
from config import Config
from flask_admin import Admin
from flask_admin.contrib.sqla import ModelView
import click

# Inicializamos as extensões fora da factory
db = SQLAlchemy()
login_manager = LoginManager()

def create_app():
    app = Flask(__name__)
    
    app.config.from_object(Config)
    
    db.init_app(app)
    login_manager.init_app(app)
    login_manager.login_view = 'main.index'

    # ADICIONANDO A EXTENSÃO DE ADMINISTRAÇÃO
    from .models import Usuario, Treino, TipoExercicio
    
    admin = Admin(app, name='Fitness Log Admin', template_mode='bootstrap4')
    admin.add_view(ModelView(Usuario, db.session))
    admin.add_view(ModelView(Treino, db.session))
    admin.add_view(ModelView(TipoExercicio, db.session))

    # Registro de modelos para o login_manager
    @login_manager.user_loader
    def load_user(user_id):
        return Usuario.query.get(int(user_id))

    # Registro de Blueprints
    from .routes import main
    app.register_blueprint(main)

    # COMANDOS CLI
    @app.cli.command("create-db")
    def create_db():
        """Cria as tabelas do banco de dados."""
        with app.app_context():
            db.create_all()
            print("Banco de dados criado com sucesso!")

    @app.cli.command("add-user")
    @click.argument("nome")  # Registra o primeiro argumento (Nome Real)
    @click.argument("user")  # Registra o segundo argumento (Login)
    @click.option("--password", prompt=True, hide_input=True)
    @click.option("--email", prompt=True)
    def add_user(nome, user, password, email):
        """Adiciona um novo usuário via CLI."""
        from werkzeug.security import generate_password_hash
        from .models import Usuario
        
        with app.app_context():
            senha_hash = generate_password_hash(password)
            novo_usuario = Usuario(
                nome=nome,       # Agora a variável 'nome' está definida na função
                usuario=user, 
                email=email, 
                senha=senha_hash, 
                nivel='Admin'
            )
            db.session.add(novo_usuario)
            db.session.commit()
            print(f"Usuário {user} ({nome}) criado com sucesso!")

    return app
