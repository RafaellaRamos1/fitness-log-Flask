from . import db
from flask_login import UserMixin

# Arquitetura: Mapeamento Objeto-Relacional (ORM)
# Transforma a tabela 'usuarios' do SQL em uma Classe Python
class Usuario(db.Model, UserMixin):
    __tablename__ = 'usuarios'
    
    id = db.Column(db.Integer, primary_key=True)
    usuario = db.Column(db.String(50), unique=True, nullable=False)
    email = db.Column(db.String(100))
    senha = db.Column(db.String(255), nullable=False)
    nivel = db.Column(db.String(20), default='Comum')

    # Método obrigatório para o Flask-Login gerenciar a sessão
    def get_id(self):
        return str(self.id)