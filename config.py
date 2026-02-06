import os
from dotenv import load_dotenv

# Carrega as variáveis do arquivo .env
load_dotenv()

class Config:
    # Arquitetura: Centralização de constantes do sistema
    SECRET_KEY = os.getenv('SECRET_KEY', 'chaveSecreta')
    SQLALCHEMY_DATABASE_URI = os.getenv('DATABASE_URL')
    SQLALCHEMY_TRACK_MODIFICATIONS = False
