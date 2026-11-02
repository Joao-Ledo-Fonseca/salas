# Documentação Técnica — Implementação

## Objetivo
Esta aplicação gere reservas de salas e laboratórios, com controlo de acesso por níveis (User, Gestor, Admin, SuperAdmin), relatórios, estatísticas e temas configuráveis.

## Tecnologia
- PHP 8.0+ (`PDO`, `pdo_mysql`, `session`)
- MySQL 5.7+
- Apache (`.htaccess`, `AllowOverride All`)
- Autoload PSR-4 via Composer (`Salas\` -> `app/src/`)

## Estrutura técnica

### `app/src/`
- `config/` — `AppConfig`, `bootstrap.php`, `routes.php`
- `core/` — Auth, HTTP, Validation, Persistence, Theme, Support
- `controllers/` — MVC controllers
- `models/` — Domain models (`Usuario`, `Reserva`, `Permissoes`, etc.)
- `views/` — Views PHP (`Relatorios.php`, `Estatisticas.php`, etc.)
- `tests/` — Smoke / Unit tests

### `app/src/views/` — Cabeçalho
As views `Relatorios.php` e `Estatisticas.php` incluem `cabecalho.php` (Padrão A para relatórios) via:
```php
<?php $acaoPagina = 'relatorios'; include __DIR__ . "/cabecalho.php"; ?>
```
Variáveis esperadas: `$acaoPagina`, `$tipo`, `$mostrarNavEsq`, `$mostrarNavDir`, `$titulo`, `$hoje`, `$inicio`, `$fim`, `$dia_anterior`, `$dia_posterior`.

### Autenticação
- `Authenticator` valida credenciais (`CredentialRepository`)
- `SessionManager` gere a identidade (chaves legadas: `user_id`, `user_nome`, `user_nivel`)
- `SalasSessionAdapter` mantém compatibilidade com sessões antigas
- Suporte a hashes MD5 legados com migração automática

### Configuração
- Prioridade: variáveis ambiente (`.env`) > `config.json`
- `.env` suportado (`DB_HOST`, `DB_USER`, `DB_PASSWORD`, `APP_THEME`, etc.)
- `config.json`: fallback para desenvolvimento
- `APP_DEBUG=0` recomendado em produção

### Temas (`app/themes/`)
- `themes.json`: manifesto (`default`, `available`)
- `light.json` / `dark.json`: cores (`colors`), dashboard (`dashboard`), fonte (`fontFamily`, `fontSizeBase`)
- `ThemeLoader` expõe `$cfg->theme`, `$cfg->availableThemes`
- Troca: `APP_THEME`, `?theme=dark` ou `window.SalasTheme.set()`

### Deployment
- `DocumentRoot` deve apontar para `app/public/`
- `app/src/` e `app/config.json` protegidos por `.htaccess`
- Modo A: `VirtualHost` (`ServerName salas.test`, `DocumentRoot .../app/public`)
- Modo B: `Alias /salas/` no `localhost` (`DocumentRoot` mantido em `www/`)
- `config.json` nunca substituído durante atualização; fazer backup antes

### Segurança
- `.htaccess` em `app/` bloqueia acesso direto a `src/` e `config.json`
- `.htaccess` em `public/` define headers de segurança (`X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`)
- Credenciais apenas em `config.json` (fora do `DocumentRoot`) ou `.env`
- Não usar conta `root` em produção

### Testes
- `SmokeTest.php`: valida manifestos de tema, rotas, estrutura
- `ThemeLoaderTest.php`: valida temas, variáveis CSS, `Theme` object

### Utilizadores iniciais
- `superadmin` / `superadmin` (nível 3)
- `admin` / `admin` (nível 2)
- `gestor` / `gestor` (nível 1)
- `user` / `user` (nível 0)
- `sadmin` / `sadmin` criado automaticamente se a BD estiver vazia (`seedSuperAdmin`)
