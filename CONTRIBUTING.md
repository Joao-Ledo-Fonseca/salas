# Guia de contribuição

Obrigado por contribuir para o projeto **Reserva de Salas**.

## Pré-requisitos
- PHP 8.0+ (recomendado 8.4+), MySQL 5.7+, Apache com `AllowOverride All`.
- Composer (para dependências de desenvolvimento).

## Configuração do ambiente
1. Clonar o repositório: `git clone <url>`
2. Copiar `app/config.example.json` para `app/config.json` e configurar a BD.
3. (Opcional) Copiar `app/.env.example` para `app/.env` e ajustar.
4. Criar a BD executando `app/sgreserva.sql` (ou `project/sgreserva.sql`).
5. Apontar o Apache para `app/public/` (VirtualHost ou Alias).
6. Instalar dependências: `composer install`.

## Padrões de código
- Seguir PSR-12.
- Métodos em camelCase, classes em PascalCase.
- Escapar todas as saídas com `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
- Usar prepared statements (PDO) — nunca concatenar SQL.
- Adicionar token CSRF em todos os formulários POST.

## Como adicionar uma nova entidade
1. Criar tabela na BD e atualizar `app/sgreserva.sql` e `project/sgreserva.sql`.
2. Criar model em `app/src/models/` (listar, abrir, salvar, excluir).
3. Criar controller em `app/src/controllers/` (salvar, excluir, cancelar, abrir, listarController, listarOpcoesController).
4. Criar views `*List.php` e `*Form.php` em `app/src/views/` (usar o layout comum).
5. Adicionar rotas em `app/src/config/routes.php`.
6. Adicionar cases no `ApplicationPageController::dispatch` e método `*Form`.
7. Adicionar permissão em `PermissoesController::$permissoes_defaults` e em `AuthorizationMiddleware`.
8. Adicionar item no menu (`menuData()`) se aplicável.
9. Atualizar `README.md`.

## Testes
```powershell
composer test      # PHPUnit
composer lint      # php -l em todos os ficheiros
composer analyse   # PHPStan (nível 5)
```

## Fluxo de trabalho
1. Criar branch: `git checkout -b feature/nome-da-feature`.
2. Fazer commits pequenos e descritivos.
3. Garantir que `composer test` e `composer lint` passam.
4. Abrir Pull Request para `master`.

## Contacto
Dúvidas: abrir issue no repositório.
