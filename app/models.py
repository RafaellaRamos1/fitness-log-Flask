from datetime import datetime
from . import db
from flask_login import UserMixin

# Arquitetura: Mapeamento Objeto-Relacional (ORM)
# Transforma a tabela 'usuarios' do SQL em uma Classe Python
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
    __tablename__ = 'tipos_exercicio'
    id = db.Column(db.Integer, primary_key=True)
    descricao = db.Column(db.String(100), nullable=False) # Ex: Corrida, Supino
    calorias = db.Column(db.Integer, nullable=False)

# Mapeamento da tabela de Treinos (Onde registra a atividade)
class Treino(db.Model):
    __tablename__ = 'treinos'
    id = db.Column(db.Integer, primary_key=True)
    data = db.Column(db.DateTime, nullable=False, default=datetime.utcnow)
    duracao = db.Column(db.Integer, nullable=False) # em minutos
    
    # Chaves Estrangeiras (Arquitetura de Relacionamento)
    usuario_id = db.Column(db.Integer, db.ForeignKey('usuario.id'), nullable=False)
    tipo_id = db.Column(db.Integer, db.ForeignKey('tipos_exercicio.id'), nullable=False)
