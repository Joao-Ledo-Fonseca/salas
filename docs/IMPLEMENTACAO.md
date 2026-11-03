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
- `Config/` — configuração da aplicação, `bootstrap.php` e rotas
- `Core/` — Auth, HTTP, Validation, Persistence, Theme e Support
- `Controllers/` — controllers MVC
- `Models/` — modelos de domínio (`Usuario`, `Reserva`, `Permissoes`, etc.)
- `views/` — Views PHP (`Relatorios.php`, `Estatisticas.php`, etc.)

Os testes ficam em `app/Tests/` (smoke, unitários e integração).

### Reservas relacionadas e relatórios semanais
- Os formulários de sala e utilizador apresentam as reservas associadas usando o componente partilhado `app/src/views/components/ReservasAssociadas.php`.
- O componente mostra estado e dados relevantes da reserva. A eliminação individual ou em lote respeita as permissões; o utilizador também pode eliminar as próprias reservas.
- Os relatórios semanais usam semanas ISO: começam à segunda-feira e terminam no domingo. As consultas usam um intervalo inclusivo no início e exclusivo na segunda-feira seguinte, evitando erros nas semanas que atravessam a mudança do ano.
- Os gráficos de reservas por data em intervalos personalizados agrupam os dias pela semana ISO.

### `app/src/views/` — Cabeçalho
As views `Relatorios.php` e `Estatisticas.php` incluem `components/cabecalho.php` para apresentar navegação e filtros de período:
```php
<?php $acaoPagina = 'relatorios'; include __DIR__ . "/components/cabecalho.php"; ?>
```
Variáveis esperadas: `$acaoPagina`, `$tipo`, `$mostrarNavEsq`, `$mostrarNavDir`, `$titulo`, `$hoje`, `$inicio`, `$fim`, `$dia_anterior`, `$dia_posterior`.

### Ações partilhadas das vistas
- `components/AcoesLista.php` centraliza o botão de criação, formulário e token CSRF nas listas de categorias, períodos, usos e utilizadores.
- `components/AcoesFormulario.php` centraliza Guardar/Excluir/Cancelar nos formulários CRUD, suportando cancelamento por submissão ou redirecionamento para preservar os fluxos existentes.

### Autenticação
- `Authenticator` valida credenciais (`CredentialRepository`)
- `SessionManager` gere a identidade (chaves legadas: `user_id`, `user_nome`, `user_nivel`)
- `SalasSessionAdapter` mantém compatibilidade com sessões antigas
- Suporte a hashes MD5 legados com migração automática

### Autorização e permissões
- A atribuição de nível é validada no servidor: um utilizador só pode atribuir níveis iguais ou inferiores ao seu próprio nível.
- No formulário de permissões, uma checkbox ausente no pedido é tratada como desmarcada; guardar aplica a matriz completa, permitindo revogar permissões.
- As permissões são lidas da base de dados, não da sessão; alterações e revogações aplicam-se no próximo pedido de cada utilizador.
- `A_RegNovoUtilizador` controla o acesso público ao registo: oculta o link quando desativada e bloqueia pedidos diretos ao formulário ou à submissão.
- `A_PerfilUtilizador` controla a opção **Perfil** no menu e a autorização do utilizador para abrir/alterar o próprio perfil. Desativá-la oculta a opção e bloqueia o acesso direto à rota; a permissão `M_Utilizadores` continua a controlar a edição de outros utilizadores.
- Nomes de utilizador e e-mails são verificados contra registos existentes; os índices únicos da base de dados continuam a proteger contra duplicados concorrentes.
- As ações de rota exigem uma associação explícita a uma permissão existente em `Permissoes::DEFAULTS`; ações sem associação válida são bloqueadas. A página de permissões lista essas rotas em avisos para facilitar a configuração.
- O dashboard usa `M_Dashboard`; a lista de imagens usa `M_Salas`. Ambas as ações também passam pela autorização antes de responder.
- A autorização, os níveis, o mapeamento rota-permissão e a lista de avisos são centralizados em `AuthorizationService`. O menu consulta as permissões pela ação da rota em vez de manter um mapeamento separado.
- A verificação de autorização não altera a tabela de permissões: linhas ausentes usam os valores iniciais de `Permissoes::DEFAULTS`; a inicialização/manutenção persistente dos defaults é feita ao abrir a gestão de permissões.

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
- `Unit/ThemeLoaderTest.php`: valida temas, variáveis CSS e o objeto `Theme`
- `Unit/ReservaWeeklyReportsTest.php`: valida intervalos ISO e agregações semanais, incluindo a mudança do ano
- `Unit/ReservasAssociadasTest.php`: valida a apresentação das reservas associadas e a eliminação de reserva do próprio utilizador
- `Unit/ViewComponentsTest.php`: valida componentes de vista, incluindo ações comuns de listas e formulários CRUD
- `Unit/PermissionPhaseOneTest.php`: valida limites de atribuição de nível e gravação completa do formulário de permissões
- `Integration/PermissoesIntegrationTest.php`: valida atualizações/revogações com sessão ativa, defaults sem efeitos laterais, rotas permitidas/bloqueadas e inventário de políticas
- `Integration/PublicRegistrationIntegrationTest.php`: valida controlo do registo público, retorno de sucesso e unicidade de nome/e-mail

Executar as suites com `.\vendor\bin\phpunit.bat -c phpunit.xml` e `.\vendor\bin\phpunit.bat -c phpunit.integration.xml`. A suite de integração requer a base de dados configurada.

### Utilizadores iniciais
- `superadmin` / `superadmin` (nível 3)
- `admin` / `admin` (nível 2)
- `gestor` / `gestor` (nível 1)
- `user` / `user` (nível 0)
- `sadmin` / `sadmin` criado automaticamente se a BD estiver vazia (`seedSuperAdmin`)
