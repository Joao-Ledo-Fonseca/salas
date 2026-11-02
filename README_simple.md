# Reserva de Salas

Aplicação web para gerir reservas de salas e laboratórios.

## Funcionalidades

- Reservas de salas com filtro por categoria, período e dados
- Relatórios e estatísticas (ocupação, uso, data, sala)
- Gestão de categorias, períodos, salas, utilizadores, permissões
- Dashboard com filtro por categoria e utilizador (se autorizado)
- Temas configuráveis (claro/escuro) via JSON
- Controle de acesso por níveis (User, Gestor, Admin, SuperAdmin)

## Requisitos

- Apache com `.htaccess` (`AllowOverride All`)
- PHP 8.0+ (`PDO`, `pdo_mysql`, `session`)
- MySQL 5.7+

## Deploy rápido

1. Crie a base de dados (`app/sgreserva.sql`)
2. Configure `app/config.json` (copiar de `config.example.json`)
3. Aponte `DocumentRoot` para `app/public/`
4. Amarante `.env` (opcional) para sobrepor `config.json`

## Documentação técnica

Para detalhes de implementação (autenticação, router, temas, testes, deploy detalhado), consulte `docs/IMPLEMENTACAO.md`.

## Utilizadores iniciais

| Utilizador | Email | Nível | Palavra-passe |
|---|---|---|---|
| superadmin | superadmin@local | 3 - SuperAdmin | superadmin |
| admin | admin@admin | 2 - Admin | admin |
| gestor | gestor@site.com | 1 - Gestor | gestor |
| user | user@user | 0 - User | user |
