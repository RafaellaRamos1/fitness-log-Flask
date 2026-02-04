# 🏋️ Fitness Log - Sistema de Registro de Treinos

Sistema web desenvolvido para gerenciamento de treinos diários, permitindo o registro de atividades físicas, cálculo de calorias gastas e acompanhamento histórico.

Projeto desenvolvido para a disciplina de Desenvolvimento Web II.

## 🚀 Funcionalidades

* **Autenticação:** Sistema de Login e Logout seguro (Senhas com Hash).
* **Dashboard:** Visualização de todos os treinos com cálculo automático de calorias totais.
* **CRUD Completo:** Registrar, Editar e Excluir treinos.
* **Tipos de Exercício:** Cadastro de exercícios personalizados (Ex: Crossfit, Zumba) que ficam salvos para o usuário.
* **Filtros:** Busca de treinos por data específica.
* **Gestão de Usuários:** O Administrador pode cadastrar novos usuários para o sistema.

## 🛠️ Tecnologias Utilizadas

* **PHP 8+** (Estruturado/Procedural)
* **MySQL** (Banco de Dados Relacional)
* **Bootstrap 5** (Interface Responsiva)
* **XAMPP** (Servidor Apache Local)

## 📦 Como Rodar o Projeto

Siga os passos abaixo para instalar o sistema na sua máquina:

### 1. Pré-requisitos
Certifique-se de ter o **XAMPP** instalado e os serviços **Apache** e **MySQL** rodando.

### 2. Clonar o Repositório
Abra o terminal na pasta `htdocs` do seu XAMPP (`C:\xampp\htdocs` no Windows ou `/opt/lampp/htdocs` no Linux) e execute:

```bash
git clone [https://github.com/milenabmota/Fitness-Log.git](https://github.com/milenabmota/Fitness-Log.git) fitness_log
```

### 3. Configurar o Banco de Dados
* 1. Acesse o phpMyAdmin (http://localhost/phpmyadmin).
* 2. Crie um novo banco de dados com o nome: fitness_db.
* 3. Clique na aba Importar.
* 4. Selecione o arquivo fitness_db.sql que está dentro da pasta do projeto baixado.
* 5. Clique em Executar para criar as tabelas e usuários padrões.

### 4. Configuração de Conexão (Opcional)
O sistema já vem configurado para rodar localmente. Caso tenha problemas de conexão, verifique o arquivo config/database.php:
```bash
$servername = "127.0.0.1"; // Ou "localhost"
$username = "root";
$password = "";
$dbname = "fitness_db";
```
## 🖥️ Como Acessar
#### 1. Abra seu navegador
#### 2. Acesse: `http://localhost/fitness-log` (ou o nome da pasta que você clonou).

🔑 Credenciais de Acesso (Admin)
Para o primeiro acesso, utilize o usuário administrador padrão :

`Usuário: admin`

`Senha: 123456`
