# Reserva de Salas

Aplicação web para gerir reservas de salas e laboratórios, desenvolvida em PHP e MySQL.

## Requisitos

- Apache com suporte a PHP e ficheiros `.htaccess` (`AllowOverride All`).
- PHP 8.0 ou superior, com as extensões `PDO`, `pdo_mysql` e `session` ativadas no PHP usado pelo Apache. O código utiliza `str_contains()` e `str_ends_with()`, indisponíveis no PHP 7.4.
- Em produção, use uma versão do PHP ainda suportada; recomenda-se PHP 8.4 ou superior.
- MySQL 5.7 ou superior.
- Acesso ao Apache, PHP e MySQL pelo mesmo servidor (ou conectividade de rede entre eles).

## Estrutura de pastas

```text
C:\wamp64\dev\salas\
  .git\                    # repositório Git, versiona app/ e project/
  .gitignore
  README.md                # documentação do projeto
  app\                     # pacote completo para distribuição/deployment
    LICENSE
    config.example.json    # modelo; copiar para config.json e configurar
    config.json            # configuração local, ignorada pelo Git
    sgreserva.sql          # dump para instalação inicial
    .htaccess            # impede acesso web direto a app/
    src\                  # código privado da aplicação; não publicar como DocumentRoot
      config\             # configuração, arranque e rotas
      application\        # serviços, páginas e roteamento
      core\      # auth, HTTP, validação, persistência e suporte
      controllers\        # controladores MVC
      models\             # modelos e acesso à base de dados
      views\              # páginas e componentes de apresentação
    public\                # único diretório que deve ser servido pelo Apache
      index.php            # front controller
      css\
      datatables\
      img\
      js\
    tests\               # smoke tests
  project\                 # ficheiros de desenvolvimento; não é necessário em produção
    .vscode\
    salas.code-workspace
    sgreserva.sql          # dump inicial da base de dados
    ...
```

## Descrição das pastas em `app/src/`

- `config/` — configuração da aplicação (`AppConfig`), arranque (`bootstrap.php`) e rotas (`routes.php`).
- `core/` — auth, HTTP, validação, persistência (Database, Repositories) e suporte.
- `controllers/` — controladores HTTP/MVC que coordenam pedidos, autorização e preparação de dados.
- `application/` — serviços, páginas e roteamento.
- `models/` — modelos de domínio e acesso a dados (compatibilidade e consultas).
- `views/` — vistas PHP responsáveis apenas pela apresentação.


O diretório `app/` é o pacote de distribuição: inclui o código e ficheiros necessários à execução, este README, a licença e o dump SQL. O ficheiro `config.json` contém credenciais específicas do ambiente e é ignorado pelo Git. A árvore inteira `C:\wamp64\dev\salas`, incluindo `app/config.json`, fica fora do `DocumentRoot` geral `C:\wamp64\www`; nos dois modos abaixo, o Apache só publica `app/public/`. Crie o `config.json` a partir de `config.example.json` em cada ambiente e nunca publique credenciais no repositório. Para deployment, copie o conteúdo de `app/` e forneça separadamente o `config.json` seguro do servidor. O repositório Git fica na raiz `C:\wamp64\dev\salas` e versiona `app/` e `project/`; faça as alterações ao código dentro de `app/`. O diretório `project/` contém ficheiros locais/de desenvolvimento e não é necessário em produção. Não coloque a raiz do repositório, `app/src/` nem `project/` sob um `DocumentRoot` público.

## Autenticação e sessão

O núcleo reutilizável da autenticação está em `app/src/core/Auth/`: `Authenticator` valida credenciais através do contrato `CredentialRepository`, e `SessionManager` gere a identidade e o ciclo de vida da sessão PHP. Estes componentes não dependem do modelo de utilizador, da base de dados ou das páginas da aplicação.

A integração específica com o sistema de reservas está em `app/src/core/Auth/`. `SalasCredentialRepository` liga o contrato de credenciais ao modelo `Usuario`; `SalasSessionAdapter` mantém as chaves de sessão legadas (`user_id`, `user_nome`, `user_nivel` e `ultima_atividade`) para que as páginas, as permissões e as reservas existentes continuem a funcionar. Sessões já iniciadas antes desta separação são aceites e adotadas pelo novo gestor sem exigir novo login. O suporte a hashes MD5 legados e a sua migração após autenticação válida também foi mantido.

Esta separação não inclui a manutenção de perfis nem as permissões por níveis: continuam a pertencer à aplicação. O esquema da base de dados não muda.

A configuração da aplicação é carregada uma vez por pedido por `AppConfig`; o front controller cria um `Database` partilhado e injeta-o nos controllers e modelos. O wrapper PDO abre a ligação de forma preguiçosa na primeira consulta e reutiliza-a durante o pedido. Os modelos não leem credenciais globais nem criam ligações próprias.

## Controllers, vistas e apresentação

O front controller usa `Router` para encaminhar pedidos e `AuthorizationMiddleware` para autorização. `ApplicationPageController` coordena as operações e prepara os dados de apresentação. `ViewRenderer` centraliza a renderização das vistas. As vistas recebem esses dados e limitam-se a construir o HTML e escapar os valores apresentados; não instanciam nem chamam controllers.

O fluxo `?acao=login` é coordenado por `LoginController`, que trata autenticação ou registo e prepara o estado consumido pela vista `login.php`. A página inicial passa por `DashboardPageController`, que interpreta os filtros e prepara categorias, períodos, salas, reservas e datas para `index.php`; o `DashboardController` fornece dados estruturados para o mapa.

Os relatórios seguem a mesma separação: `DashboardController` devolve arrays de reservas e totais; `relatorios.php` e `relatorios2.php` renderizam tabelas e mensagens. A listagem de categorias, períodos, salas e utilizadores também recebe arrays dos controllers e constrói as linhas nas respetivas vistas. A vista de permissões cria as caixas de seleção a partir dos dados devolvidos pelo controller. O controller de reservas devolve os estados como dados para `reserva_form.php` gerar as opções do formulário.

Os redirecionamentos da lista de salas preservam os filtros na query string. A gravação e eliminação de reservas passam pelo controller e devolvem ao front controller o texto esperado pelo AJAX; o controller não escreve HTML nem termina a execução do PHP.

As vistas não leem parâmetros do pedido nem chamam controllers: recebem dados já preparados, apresentam formulários e geram HTML. O `Router` encaminha os pedidos, o `AuthorizationMiddleware` valida acessos e o `ApplicationPageController` coordena as operações. Os controllers de domínio usam serviços e repositórios; os modelos e repositórios concentram o acesso à base de dados. A validação de entrada é feita por `Validator`, a autenticação e autorização são resolvidas antes de incluir qualquer vista, e a renderização é centralizada em `ViewRenderer`.

## Deployment de uma atualização

1. Teste as alterações em `app/` no ambiente local e guarde-as no Git a partir de `C:\wamp64\dev\salas`.
2. Faça cópia de segurança da versão de produção e do `config.json` de produção. Este ficheiro fica em `app/config.json`, fora do DocumentRoot; não o substitua pelas credenciais locais durante a atualização.
3. Copie a pasta `app/` para o servidor, mantendo a estrutura `app/src/` e `app/public/`. Não copie `project/` nem `.git/`.
4. Garanta que o `app/config.json` de produção existe e mantém as credenciais corretas. Se a cópia da aplicação o tiver substituído, restaure a cópia de segurança. Não substitua a base de dados de produção pelo dump; use `app/sgreserva.sql` apenas na instalação inicial, ou quando uma migração da base de dados for explicitamente necessária.
5. Confirme que o VirtualHost aponta para `app/public/`, valide a configuração do Apache e reinicie/recarregue o serviço se necessário.
6. Verifique o acesso, a ligação à base de dados e o carregamento dos recursos antes de dar a atualização por concluída.

## Preparar e iniciar a aplicação

1. Instale e inicie o Apache, o PHP e o MySQL. Em WampServer, confirme que o ícone do servidor fica verde e que o PHP ativo no Apache tem `PDO` e `pdo_mysql` ativados.
2. Crie a base de dados importando `app/sgreserva.sql` através do phpMyAdmin (separador **Importar**) ou da linha de comandos:

   ```cmd
   "C:\wamp64\bin\mysql\mysql8.4.7\bin\mysql.exe" -u root -p < "C:\wamp64\dev\salas\app\sgreserva.sql"
   ```

   Ajuste o caminho do executável à versão de MySQL instalada. O dump cria e seleciona a base de dados `sgreserva`; a conta usada para importar precisa de permissão para criar a base de dados e as tabelas. O original de desenvolvimento continua em `project/sgreserva.sql`.
3. Crie uma conta MySQL própria para a aplicação e conceda-lhe acesso apenas à base de dados `sgreserva`. Por exemplo, substitua a palavra-passe do exemplo por uma palavra-passe forte:

   ```sql
   CREATE USER 'sgreserva'@'localhost' IDENTIFIED BY 'substituir-por-uma-palavra-passe-forte';
   GRANT SELECT, INSERT, UPDATE, DELETE ON sgreserva.* TO 'sgreserva'@'localhost';
   ```

   Se o MySQL estiver noutro servidor, ajuste o host permitido (`localhost`) e as regras de rede para corresponder à instalação.
4. Crie `app/config.json` copiando `app/config.example.json` e edite-o com os dados da ligação à base de dados:

   ```json
   {
     "db_host": "127.0.0.1",
     "db_user": "sgreserva",
     "db_senha": "substituir-por-uma-palavra-passe-forte",
     "db_banco": "sgreserva",
     "db_porta": 3306,
     "url_base_path": "auto"
   }
   ```

   `url_base_path: "auto"` deteta automaticamente o prefixo do pedido: vazio na raiz de um VirtualHost e `/salas` quando a aplicação é servida por Alias. Também é possível fixar o valor como `""` ou `"/salas"`, respetivamente. O Apache precisa de permissão de leitura no ficheiro. Mantenha-o fora do DocumentRoot, limite as permissões de leitura à conta do serviço e não o publique nem o inclua em pacotes ou backups acessíveis pela web. Guarde cópias de segurança das credenciais apenas em armazenamento protegido. Não use a conta `root` em produção. `core/app/config/bootstrap.php` lê esse JSON e lança um erro explícito se faltar, estiver inválido ou tiver campos incorretos.
5. Configure o modo de acesso pretendido abaixo, valide a configuração, reinicie ou recarregue o Apache e abra o endereço correspondente no navegador. É possível manter os dois modos ativos em simultâneo.

## Opção A — produção com VirtualHost (recomendada)

O `DocumentRoot` aponta diretamente para `C:\wamp64\dev\salas\app\public`. Assim, o código privado e o repositório ficam fora da área publicada. Com `url_base_path` em `auto` (ou vazio), a aplicação constrói os URLs a partir da raiz do domínio.

1. Ative a inclusão dos VirtualHosts no `httpd.conf` do Apache. Em WampServer, confirme que a seguinte linha não está comentada:

   ```apache
   Include conf/extra/httpd-vhosts.conf
   ```

2. No `conf/extra/httpd-vhosts.conf`, adicione ou ajuste um VirtualHost:

   ```apache
   <VirtualHost *:80>
       ServerName salas.test
       DocumentRoot "C:/wamp64/dev/salas/app/public"

       <Directory "C:/wamp64/dev/salas/app/public/">
           Options -Indexes +FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

3. Para testar localmente, adicione a seguinte entrada ao ficheiro `hosts` do sistema, como administrador:

   ```text
   127.0.0.1 salas salas.test
   ```

   No Windows, o ficheiro é `C:\Windows\System32\drivers\etc\hosts`. Em produção, configure o DNS do domínio para apontar para o servidor e use o nome do domínio em `ServerName`.
4. Reinicie o Apache. Em WampServer, pode usar o menu do ícone do WampServer para reiniciar todos os serviços.
5. Aceda a `http://salas.test/` (ou ao domínio configurado).

Antes de reiniciar, valide a configuração do Apache:

```powershell
C:\wamp64\bin\apache\apache2.4.65\bin\httpd.exe -t -f C:\wamp64\bin\apache\apache2.4.65\conf\httpd.conf
```

Os caminhos do executável e da configuração variam conforme a versão e a instalação do Apache.

## Opção B — acesso por `http://localhost/salas/` com Alias

Esta opção mantém `C:\wamp64\www` como `DocumentRoot` do servidor principal e não exige criar um VirtualHost para a aplicação. O Alias publica somente `app/public/`; a raiz do repositório, `app/src/`, `app/config.json` e `project/` permanecem fora do espaço HTTP. Com `url_base_path` em `auto`, os links, formulários e recursos são gerados sob `/salas/`.

No WampServer, acrescente estas diretivas ao VirtualHost predefinido de `localhost` em `conf/extra/httpd-vhosts.conf`, sem alterar o seu `DocumentRoot`:

```apache
<VirtualHost _default_:80>
    ServerName localhost
    ServerAlias localhost
    DocumentRoot "C:/wamp64/www"
    RedirectMatch 301 ^/salas$ /salas/
    Alias /salas/ "C:/wamp64/dev/salas/app/public/"

    <Directory "C:/wamp64/dev/salas/app/public/">
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require local
    </Directory>
</VirtualHost>
```

Reinicie ou recarregue o Apache e aceda a `http://localhost/salas/`. O endereço `/salas` sem a barra final é normalmente redirecionado pelo Apache para `/salas/`.

Se o Apache não usar VirtualHosts, declare o mesmo `Alias /salas/` e o respetivo bloco `<Directory>` na configuração global. Use `Require all granted` apenas se o servidor tiver de aceitar ligações remotas; proteja-o com firewall e HTTPS.


## Permissões

As permissões são geridas em `PermissoesController` (`app/src/controllers/PermissoesController.php`) e armazenadas na base de dados. A aplicação cria automaticamente as permissões em falta a partir dos valores por defeito (`permissoes_defaults`). Os níveis de utilizador são: 0 = User, 1 = Gestor, 2 = Admin, 3 = SuperAdmin (tem todas as permissões).

Permissões relevantes:

- `M_Reservas` — acesso ao formulário de reservas e exclusão de reservas.
- `M_EliminarReservasUtilizador` — autoriza eliminar todas as reservas de um utilizador (no formulário de utilizador).
- `M_FiltroUtilizador` — autoriza o filtro por utilizador no dashboard (apresentado a Gestores, Admins e SuperAdmins; oculto para Users).
- `M_Categorias`, `M_Usos`, `M_Salas`, `M_Periodos`, `M_Utilizadores`, `M_Permissoes`, `M_Estatisticas`, `M_Relatorios` — acesso aos respetivos menus.

## Funcionalidades recentes

- Dashboard com filtro por categoria (salas) e, para utilizadores autorizados, filtro por utilizador (dropdown à direita do filtro de categorias), com tooltips identificativos.
- Tabela do dashboard compacta, com scroll interno, para melhor aproveitamento do ecrã.
- Formulário de utilizador com lista de reservas do utilizador (compacta, com scroll próprio, status por extenso e botão de eliminação por linha, além de eliminação de todas as reservas).
- Entidade "Uso" (tabela, form, lista, permissão `M_Usos`) e dropdown de usos no formulário de reservas.
- Lista de salas com filtro por categoria e estado (activas/inactivas/todas).
- Formulário de salas com overlay de seleção de imagem (miniaturas existentes, carregar nova, remover). Imagens gravadas sem `addslashes` (prepared statements).


## Configuração e deploy

### Autoloading (Composer)
A aplicação usa PSR-4 autoloading via Composer. Após clonar, execute:
```powershell
composer install
composer dump-autoload
```
As classes estão nos namespaces `Salas\\Controllers`, `Salas\\Models`, `Salas\\core\\*`, etc.

### Configuração unificada (.env + config.json)
A aplicação suporta duas fontes de configuração, por ordem de prioridade:
1. **Variáveis de ambiente** (`.env` em `app/` ou variáveis do servidor) — sobrepõem tudo.
2. **`config.json`** em `app/` — usado como fallback se não existir `.env`.

Em produção, recomenda-se usar apenas variáveis de ambiente (Apache `SetEnv`, Nginx `fastcgi_param`, ou sistema). O `config.json` é útil para desenvolvimento local.

### Variáveis de ambiente
A aplicação suporta um ficheiro `.env` em `app/` (copiar de `app/.env.example`) com as chaves `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`, `APP_URL_BASE` (ou `auto`). Os valores do `.env` sobrepõem os do `config.json`. O `.env` é ignorado pelo Git.

### Segurança
- `app/src/` e `app/config.json` são bloqueados ao acesso web via `.htaccess`.
- `app/public/.htaccess` define cabeçalhos de segurança (X-Frame-Options, X-Content-Type-Options, Referrer-Policy).
- Em produção, defina `APP_DEBUG=0` para não expor detalhes de erros.

### Deploy
Copiar o conteúdo de `app/` para o servidor, fornecendo separadamente o `config.json`/`.env` seguro. Excluir `tests/` e `.git/`.


## Contribuir

Consulte o [guia de contribuição](CONTRIBUTING.md) e o [diagrama ER](docs/er-diagram.md).

## Testes

Executar os smoke tests:

```powershell
php app/tests/SmokeTest.php
```

Ver `app/tests/README.md` para mais detalhes.


## Verificação após a instalação

- A página inicial abre em `http://salas/` ou `http://salas.test/` (VirtualHost) e em `http://localhost/salas/` (Alias).
- Os estilos, imagens, scripts, formulários e rotas continuam sob o prefixo correto em ambos os modos.
- A aplicação consegue ligar à base de dados `sgreserva`.
- As páginas e operações que leem ou alteram dados funcionam com a conta MySQL da aplicação.
- A raiz do repositório, `app/src/`, `app/config.json` e `project/` não são acessíveis diretamente através do navegador.

## Utilizadores iniciais

O dump `sgreserva.sql` disponibiliza os seguintes utilizadores (a palavra-passe é igual ao nome de utilizador):

| Utilizador | Email | Nível | Palavra-passe |
|---|---|---|---|
| superadmin | superadmin@local | 3 - SuperAdmin | superadmin |
| admin | admin@admin | 2 - Admin | admin |
| user | user@user | 0 - User | user |
| gestor | gestor@site.com | 1 - Gestor | gestor |

> Nota: se a base de dados estiver vazia, o `Usuario::seedSuperAdmin()` cria automaticamente o utilizador `sadmin` com a palavra-passe `sadmin` (nível SuperAdmin). Altere estas palavras-passe ou desative as contas iniciais após a primeira entrada e antes de disponibilizar o sistema a utilizadores.
