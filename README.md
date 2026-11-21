# NexaSecure Solutions

API REST desenvolvida em PHP e MySQL para gerenciamento de serviços de cibersegurança.

## Sobre o projeto

A NexaSecure Solutions é um projeto acadêmico que tem como objetivo organizar e gerenciar informações relacionadas à segurança da informação, permitindo o controle de clientes, ativos e ordens de serviço.

## Funcionalidades

- Cadastro, consulta, atualização e exclusão de clientes.
- Cadastro, consulta, atualização e exclusão de ativos.
- Cadastro, consulta, atualização e exclusão de ordens de serviço.
- Integração com banco de dados MySQL.
- Comunicação por meio de requisições HTTP e respostas JSON.

## Tecnologias utilizadas

- PHP
- MySQL
- PDO
- Visual Studio Code
- Postman
- Git e GitHub

## Estrutura do projeto

```text
nexasecureAPI/
├── api/
│   ├── clientes.php
│   ├── ativos.php
│   ├── ordens_servico.php
│   └── ...
├── database/
│   └── nexasecure.sql
├── .gitignore
└── README.md
```

## Banco de dados

O banco de dados utiliza MySQL e possui tabelas para:

- Clientes
- Ativos
- Incidentes
- Serviços
- Técnicos
- Ordens de serviço
- Compliance
- Relacionamento entre clientes e compliance
- Planos de resposta

O arquivo `database/nexasecure.sql` contém a estrutura do banco de dados, sem registros de clientes.

## Como executar o projeto

1. Instale o PHP e o MySQL.
2. Importe `database/nexasecure.sql` no MySQL.
3. Copie `api/config.example.php` para `api/config.local.php`.
4. Configure as credenciais locais do banco no arquivo `config.local.php`.
5. No terminal, dentro da pasta do projeto, execute:

```bash
php -S localhost:8000 -t api
```

6. Acesse `http://localhost:8000`.

## Objetivo acadêmico

Projeto desenvolvido como parte de uma atividade acadêmica, com foco na aplicação prática de desenvolvimento de APIs, operações CRUD, integração com banco de dados e organização de código.

## Autor

Desenvolvido como projeto acadêmico de tecnologia e desenvolvimento de sistemas.