# Documentação da API - Gerenciador de Despesas

Esta documentação descreve os endpoints disponíveis na API de Gerenciamento de Despesas Pessoais.

## Autenticação

A API utiliza autenticação via **Token JWT (JSON Web Token)**. Para acessar os endpoints protegidos, é necessário primeiro obter um token através do endpoint de login, os tokens possuem 1 hora de validade.

O token deve ser enviado em todas as requisições para endpoints protegidos no cabeçalho `Authorization`.

**Formato:** `Authorization: Bearer <seu_token_jwt>`

---

## 1. Endpoints de Usuário

### 1.1. Registrar um Novo Usuário

Permite que um novo usuário se cadastre no sistema. A senha é salva de forma criptografada.

- **URL:** `/users/register`
- **Método:** `POST`
- **Autenticação:** Nenhuma
- **Corpo da Requisição (JSON):**

```json
{
    "email": "usuario@exemplo.com",
    "password": "sua_senha"
}
```

- **Resposta de Sucesso (201 Created):**

```json
{
    "message": "User created successfully!",
    "id": 1,
    "email": "usuario@exemplo.com"
}
```

- **Resposta de Erro (422 Unprocessable Entity):**

```json
{
    "errors": {
        "email": [
            "Email \"usuario@exemplo.com\" has already been taken."
        ]
    }
}
```

### 1.2. Fazer Login

Autentica um usuário e retorna um token JWT.

- **URL:** `/users/login`
- **Método:** `POST`
- **Autenticação:** Nenhuma
- **Corpo da Requisição (JSON):**

```json
{
    "email": "usuario@exemplo.com",
    "password": "sua_senha_segura"
}
```

- **Resposta de Sucesso (200 OK):**

```json
{
    "message": "User authenticated successfully!",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3Mi..."
}
```

- **Resposta de Erro (401 Unauthorized):**

```json
{
    "error": "Invalid credentials."
}
```

---

## 2. Endpoints de Despesas

**Atenção:** Todos os endpoints abaixo requerem autenticação.

### 2.1. Listar Despesas

Retorna uma lista paginada das despesas do usuário autenticado.

- **URL:** `/expenses`
- **Método:** `GET`
- **Autenticação:** Obrigatória
- **Parâmetros de URL (Query Params):**
    - `category` (opcional, string): Filtra por categoria (`alimentação`, `transporte`, `lazer`).
    - `year` (opcional, integer): Filtra por ano (ex: `2025`).
    - `month` (opcional, integer): Filtra por mês (ex: `09`).
    - `day` (opcional, integer): Filtra por mês (ex: `09`).
    - `order_by_column` (opcional, string): Define a coluna para ordenação. Padrão: `expense_date`. Ex: `value` (ordena por valor).
    - `order_by_type` (opcional, string): Define o tipo de ordenação. Padrão: `SORT_DESC` (decrescente). Outra opção: `SORT_ASC` (crescente).
    - `page` (opcional, integer): Número da página. Padrão: `1`.

- **Resposta de Sucesso (200 OK):**

```json
{
    "success": true,
    "message": "Expenses listed successfully.",
    "data": [
        {
            "id": 1,
            "description": "Almoço de trabalho",
            "category": "alimentação",
            "value": "35.50",
            "expense_date": "2025-09-09"
        }
    ]
}
```

### 2.2. Criar uma Nova Despesa

Registra uma nova despesa para o usuário autenticado.

- **URL:** `/expenses`
- **Método:** `POST`
- **Autenticação:** Obrigatória
- **Corpo da Requisição (JSON):**

```json
{
    "description": "Corrida de Uber",
    "category": "transporte",
    "value": 15.75,
    "expense_date": "2025-09-10"
}
```
- **Resposta de Sucesso (201 Created):**

```json
{
    "success": true,
    "message": "Expense created successfully!",
    "data": {
        "id": 2,
        "description": "Corrida de Uber",
        "category": "transporte",
        "value": "15.75",
        "expense_date": "2025-09-10"
    }
}
```

### 2.3. Ver Detalhes de uma Despesa

Retorna os detalhes de uma despesa específica.

- **URL:** `/expenses/{id}`
- **Método:** `GET`
- **Autenticação:** Obrigatória
- **Resposta de Sucesso (200 OK):** (Estrutura similar à da criação)

### 2.4. Atualizar uma Despesa

Atualiza os dados de uma despesa existente.

- **URL:** `/expenses/{id}`
- **Método:** `PUT` ou `PATCH`
- **Autenticação:** Obrigatória
- **Corpo da Requisição (JSON):**

```json
{
    "description": "Cinema",
    "value": 45.00,
    "category": "lazer"
}
```
- **Resposta de Sucesso (200 OK):** (Estrutura similar à da criação, com os dados atualizados)

### 2.5. Excluir uma Despesa

Exclui uma despesa específica.

- **URL:** `/expenses/{id}`
- **Método:** `DELETE`
- **Autenticação:** Obrigatória
- **Resposta de Sucesso (200 OK):**

```json
{
    "success": true,
    "message": "Expense deleted successfully!",
    "data": null
}
```