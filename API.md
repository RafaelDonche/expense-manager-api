# Documentação da API - Gerenciador de Despesas

Esta documentação descreve os endpoints disponíveis na API de Gerenciamento de Despesas Pessoais.

## Autenticação

A API utiliza autenticação via **Token JWT (JSON Web Token)**. Para acessar os endpoints protegidos, é necessário primeiro obter um token através do endpoint de login (os tokens possuem 24 horas de validade).

O token deve ser enviado em todas as requisições para endpoints protegidos no cabeçalho `Authorization`.

**Formato:** `Authorization: Bearer <seu_token_jwt>`

---

## 1. Endpoints de Usuário

### 1.1. Registrar um Novo Usuário

Permite que um novo usuário se cadastre no sistema. A senha é salva de forma criptografada.

- **URL:** `/usuarios/registrar`
- **Método:** `POST`
- **Autenticação:** Nenhuma
- **Corpo da Requisição (JSON):**

```json
{
    "email": "usuario@exemplo.com",
    "senha": "sua_senha"
}
```

- **Resposta de Sucesso (201 Created):**

```json
{
    "message": "Usuário criado com sucesso!",
    "id": 1,
    "email": "usuario@exemplo.com"
}
```

- **Resposta de Erro (422 Unprocessable Entity):**

```json
{
    "errors": {
        "email": [
            "Este e-mail já está cadastrado."
        ]
    }
}
```

### 1.2. Fazer Login

Autentica um usuário e retorna um token JWT.

- **URL:** `/usuarios/login`
- **Método:** `POST`
- **Autenticação:** Nenhuma
- **Corpo da Requisição (JSON):**

```json
{
    "email": "usuario@exemplo.com",
    "senha": "sua_senha_segura"
}
```

- **Resposta de Sucesso (200 OK):**

```json
{
    "message": "Usuário autenticado com sucesso!",
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3Mi..."
}
```

- **Resposta de Erro (401 Unauthorized):**

```json
{
    "error": "Credenciais inválidas."
}
```

---

## 2. Endpoints de Despesas

**Atenção:** Todos os endpoints abaixo requerem autenticação.

### 2.1. Listar Despesas

Retorna uma lista paginada das despesas do usuário autenticado.

- **URL:** `/despesas`
- **Método:** `GET`
- **Parâmetros de URL (Query Params):**
    - `categoria` (opcional, string): Filtra por categoria (`Alimentação`, `Transporte`, `Lazer`).
    - `ano` (opcional, integer): Filtra por ano (ex: `2025`).
    - `mes` (opcional, integer): Filtra por mês (ex: `09`).
    - `dia` (opcional, integer): Filtra por mês (ex: `09`).
    - `coluna_ordenacao` (opcional, string): Define a coluna para ordenação. Padrão: `data`. Ex: `valor` (ordena por valor).
    - `tipo_ordenacao` (opcional, string): Define o tipo de ordenação. Padrão: `SORT_DESC` (decrescente). Outra opção: `SORT_ASC` (crescente).
    - `pagina` (opcional, integer): Número da página. Padrão: `1`.

- **Resposta de Sucesso (200 OK):**

```json
{
    "success": true,
    "message": "Despesas listadas com sucesso.",
    "data": [
        {
            "id": 1,
            "descricao": "Almoço de trabalho",
            "categoria": "Alimentação",
            "valor": "35.50",
            "data": "09/09/2025"
        },
        {
            "id": 2,
            "descricao": "Jantar em equipe",
            "categoria": "Alimentação",
            "valor": "115.80",
            "data": "09/09/2025"
        }
    ]
}
```

### 2.2. Criar uma Nova Despesa

Registra uma nova despesa para o usuário autenticado.

- **URL:** `/despesas`
- **Método:** `POST`
- **Corpo da Requisição (JSON):**

```json
{
    "descricao": "Corrida de Uber",
    "categoria": "Transporte",
    "valor": 15.75,
    "data": "09/09/2025"
}
```
- **Resposta de Sucesso (201 Created):**

```json
{
    "success": true,
    "message": "Despesa criada com sucesso!",
    "data": {
        "id": 2,
        "descricao": "Corrida de Uber",
        "categoria": "Transporte",
        "valor": "15.75",
        "data": "09/09/2025"
    }
}
```

### 2.3. Ver Detalhes de uma Despesa

Retorna os detalhes de uma despesa específica.

- **URL:** `/despesas/{id}`
- **Método:** `GET`
- **Resposta de Sucesso (200 OK):** (Estrutura similar à da criação)

### 2.4. Atualizar uma Despesa

Atualiza os dados de uma despesa existente.

- **URL:** `/despesas/{id}`
- **Método:** `PUT` ou `PATCH`
- **Corpo da Requisição (JSON):**

```json
{
    "descricao": "Cinema",
    "valor": 45.00,
    "categoria": "Lazer"
}
```
- **Resposta de Sucesso (200 OK):** (Estrutura similar à da criação, com os dados atualizados)

### 2.5. Excluir uma Despesa

Exclui uma despesa específica.

- **URL:** `/despesas/{id}`
- **Método:** `DELETE`
- **Resposta de Sucesso (200 OK):**

```json
{
    "success": true,
    "message": "Despesa excluída com sucesso!",
    "data": null
}
```