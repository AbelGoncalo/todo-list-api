# To-Do List API

API RESTful desenvolvida em Laravel para gerenciamento de tarefas (To-Do List), permitindo que cada usuário autenticado crie, visualize, atualize, delete e filtre suas tarefas por status.

## 🚀 Funcionalidades

- ✅ Cadastro de tarefas (com título obrigatório e descrição opcional)
- 📋 Listagem de tarefas do usuário autenticado
- 🔄 Atualização de status da tarefa (`pending`, `in_progress`, `completed`)
- ❌ Remoção de tarefas
- 🔎 Filtro por status
- 🔐 Autenticação de usuários com Laravel Sanctum

## 🔧 Requisitos

- PHP >= 8.2
- Composer
- MySQL ou MariaDB
- Laravel = 12


## 🚀 Tecnologias Utilizadas

- Laravel Jwt (Autenticação)
- MySQL/Mysql
- Pest PHP (Testes)
- Swagger/OpenAPI (Documentação)

---

## ⚙️ Instalação

```bash
git clone https://github.com/seu-usuario/todo-api.git
cd todo-api

composer install
cp .env.example .env
php artisan key:generate

# Configure o banco de dados no .env
php artisan migrate

