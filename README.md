# Fitness Log - Flask Professional Edition

Este projeto é um sistema de gerenciamento de treinos e calorias, reconstruído do zero utilizando o micro-framework **Flask**. A aplicação segue padrões modernos de Engenharia de Software, focando em modularização, segurança e escalabilidade.

## Decisões Arquiteturais

Para atender aos requisitos de **Arquitetura de Software**, foram aplicados os seguintes padrões:

* **Application Factory:** Centralização da criação do app no arquivo `app/__init__.py`, facilitando testes e múltiplas instâncias.
* **Modularização com Blueprints:** Separação das rotas por contexto, evitando arquivos extensos e melhorando a manutenção.
* **ORM (Object-Relational Mapping):** Uso do **SQLAlchemy** para abstração da camada de banco de dados, tratando tabelas como classes Python.
* **CLI Customizado:** Implementação de comandos de terminal via **Click** para automação de tarefas (Setup do banco e gestão de usuários).
* **Segurança:** Hashing de senhas com `scrypt` e isolamento de configurações sensíveis via variáveis de ambiente (`.env`).

---

## Guia de Instalação e Execução

Siga os passos abaixo para rodar o projeto em seu ambiente local:

### 1. Preparação do Diretório
Acesse a pasta raiz:
```bash
cd fitness-log-Flask
```
### 2. Crie e ative o ambiente isolado do Python:
```bash
python3 -m venv .venv

# No Windows:
.venv\Scripts\activate

# No Linux/Mac:
source .venv/bin/activate
```
### 3. Instale todos os pacotes necessários:
```bash
pip install -r requirements.txt
```
### 4. Configuração do Banco de Dados
Certifique-se de que o MySQL (XAMPP/MariaDB) está em execução. Crie um arquivo chamado .env na raiz do projeto e adicione suas credenciais:
```bash
SECRET_KEY=sua_chave_secreta_aqui  
DATABASE_URL=mysql+pymysql://root:@localhost/fitness_db  
```
### 5. Execute o comando para criar as tabelas automaticamente:
```bash
flask create-db
```
### 5. Criação do Usuário Administrador
Utilize o comando CLI para criar o acesso inicial sem precisar de SQL manual:
```bash
flask add-user "Seu Nome" seu_usuario --password sua_senha --email seu@email.com
```
### 6. Execução
Inicie o servidor de desenvolvimento:
```bash
flask run
```
Acesse em seu navegador: http://127.0.0.1:5000

## Tecnologias Utilizadas
Linguagem: Python 3.12+  
Framework Web: Flask  
Banco de Dados: MySQL / MariaDB  

## Interface:  
Bootstrap 5 & Jinja2 Templates  

## Gestão de Sessão: 
Flask-Login  
Painel Admin: Flask-Admin (acessível em /admin)

## Estrutura de Pastas
fitness-log-Flask/
├── app/                # Pacote principal da aplicação
│   ├── static/         # CSS e Imagens
│   ├── templates/      # HTML (Jinja2)
│   ├── models.py       # Definição das Entidades (ORM)
│   └── routes.py       # Lógica das rotas (Blueprints)
├── config.py           # Classe de configuração Python
├── run.py              # Ponto de entrada
├── .env.example        # Modelo de configuração de ambiente
└── requirements.txt    # Lista de dependências
