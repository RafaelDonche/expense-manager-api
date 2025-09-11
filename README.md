# Gerenciador de Despesas - API RESTful

![PHP](https://img.shields.io/badge/PHP-8.1-777BB4?style=for-the-badge&logo=php)
![Yii2](https://img.shields.io/badge/Yii2-Framework-green?style=for-the-badge)
![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?style=for-the-badge&logo=docker)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql)

API RESTful desenvolvida como parte de um desafio técnico para gerenciamento de despesas pessoais, utilizando PHP e o framework Yii2 em um ambiente Docker.

## Funcionalidades

* **Autenticação de Usuários:** Cadastro e Login com autenticação baseada em Token JWT.
* **Gerenciamento de Despesas (CRUD):**
    * Criação, visualização, atualização e exclusão de despesas.
    * Cada usuário tem acesso apenas às suas próprias despesas.
* **Consultas Avançadas:**
    * Listagem de despesas com filtros por categoria e período (mês/ano).
    * Ordenação dos resultados customizada por campo e ordem.
    * Paginação automática nos resultados da listagem.
* **Ambiente Padronizado:** Projeto totalmente conteinerizado com Docker, garantindo um ambiente de desenvolvimento e execução consistente e de fácil configuração.

## Decisões Técnicas

[cite_start]Para a implementação deste projeto, foram tomadas as seguintes decisões técnicas, visando seguir as boas práticas de desenvolvimento e arquitetura[cite: 4]:

* [cite_start]**Framework:** A API foi desenvolvida com **Yii2**, conforme solicitado, aproveitando sua arquitetura MVC, o poderoso ActiveRecord para manipulação de dados e seu sistema de migrações para versionamento do banco de dados.
* **Arquitetura:** Foi seguido o padrão MVC. A lógica de negócio e as regras de validação foram mantidas nos Models (`User`, `Despesa`), mantendo os Controllers enxutos e focados no controle do fluxo da requisição. O uso de `Behaviors` (`TimestampBehavior`, `BlameableBehavior`) permitiu automatizar tarefas repetitivas de forma limpa.
* [cite_start]**Banco de Dados:** **MySQL** foi o SGBD escolhido, e toda a criação e evolução do schema é gerenciada pelas **Migrations** do Yii2, garantindo consistência e reprodutibilidade do banco.
* [cite_start]**Autenticação:** A autenticação stateless foi implementada com **JSON Web Tokens (JWT)**, um padrão de mercado para APIs RESTful que garante segurança sem a necessidade de sessões no servidor.
* **Ambiente de Desenvolvimento:** **Docker e Docker Compose** foram utilizados para criar um ambiente de desenvolvimento isolado, portátil e idêntico ao de produção. [cite_start]Isso elimina problemas de configuração de máquina local e facilita a instalação do projeto, que é composto por três serviços: `webserver` (Nginx), `app` (PHP-FPM) e `db` (MySQL).
* **Design da API:** A API segue os princípios RESTful. As respostas de sucesso são padronizadas em um wrapper JSON contendo as chaves `sucesso`, `mensagem` e `dados`, melhorando a previsibilidade para os clientes da API. [cite_start]A listagem de despesas inclui metadados de paginação no corpo da resposta para facilitar o consumo por aplicações front-end.
* **Internacionalização (i18n):** O projeto foi adaptado para o português brasileiro, tanto nas rotas da API quanto nos formatos de data (`dd/mm/aaaa`), demonstrando flexibilidade e atenção à experiência do usuário. A conversão de formatos de data é tratada de forma transparente nos Models através dos eventos `beforeSave` e `afterSave`/`afterFind`.

## Pré-requisitos

* [Docker](https://www.docker.com/products/docker-desktop/)
* [Docker Compose](https://docs.docker.com/compose/install/) (geralmente já vem com o Docker Desktop)

## Instalação e Execução

Siga os passos abaixo para executar a aplicação localmente.

1.  **Clone o repositório:**
    ```bash
    git clone [https://github.com/seu-usuario/seu-repositorio.git](https://github.com/seu-usuario/seu-repositorio.git)
    cd seu-repositorio
    ```

2.  **Construa e inicie os contêineres:**
    Este comando irá construir a imagem da aplicação, baixar as imagens do Nginx e MySQL e iniciar os serviços em segundo plano.
    ```bash
    docker-compose up -d --build
    ```

3.  **Instale as dependências do PHP com o Composer:**
    Este comando é executado dentro do contêiner da aplicação.
    ```bash
    docker-compose exec app composer update
    ```

4.  **Execute as migrações do banco de dados:**
    Este comando criará as tabelas `user` e `despesa` no banco de dados de desenvolvimento.
    ```bash
    docker-compose exec app php yii migrate
    ```

Após esses passos, a API estará em execução e acessível no endereço: `http://localhost:8080`

## Utilização da API

Toda a especificação dos endpoints, incluindo URLs, parâmetros, corpos de requisição e exemplos de resposta, está documentada no arquivo `API.md`.

## Parando o Ambiente

Para parar todos os contêineres relacionados ao projeto, execute:
```bash
docker-compose down
```
Se desejar apagar também os dados do banco de dados (o volume), use:
```bash
docker-compose down -v
```