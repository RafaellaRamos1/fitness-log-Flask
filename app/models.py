from datetime import datetime
from . import db
from flask_login import UserMixin

# Arquitetura: Mapeamento Objeto-Relacional (ORM)
class Usuario(db.Model, UserMixin):
    __tablename__ = 'usuarios'
    id = db.Column(db.Integer, primary_key=True)
    nome = db.Column(db.String(100))
    usuario = db.Column(db.String(50), unique=True, nullable=False)
    email = db.Column(db.String(100))
    senha = db.Column(db.String(255), nullable=False)
    nivel = db.Column(db.String(20), default='Comum')

    # Método obrigatório para o Flask-Login gerenciar a sessão
    def get_id(self):
        return str(self.id)

# Mapeamento da tabela de Tipos de Exercício
class TipoExercicio(db.Model):
    __tablename__ = 'tipos_exercicio' # Nome da tabela no banco
    id = db.Column(db.Integer, primary_key=True)
    descricao = db.Column(db.String(100))
    calorias_por_minuto = db.Column(db.Integer)

# Mapeamento da tabela de Treinos
class Treino(db.Model):
    __tablename__ = 'treinos'
    
    id = db.Column(db.Integer, primary_key=True)
    usuario_id = db.Column(db.Integer, db.ForeignKey('usuarios.id'), nullable=False)
 
    tipo_id = db.Column(db.Integer, db.ForeignKey('tipos_exercicio.id'), nullable=False)
    
    duracao = db.Column('duracao_minutos', db.Integer, nullable=False)
    data = db.Column(db.Date)
    observacoes = db.Column(db.Text)
    data_registro = db.Column(db.DateTime, default=datetime.utcnow)

    tipo = db.relationship('TipoExercicio', backref='treinos')
