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
  app\                     # pacote completo para distribuição/deployment
    README.md
    LICENSE
    config.example.json    # modelo; copiar para config.json e configurar
    config.json            # configuração local, ignorada pelo Git
    sgreserva.sql          # dump para instalação inicial
    core\                  # código privado da aplicação; não publicar como DocumentRoot
      app\                 # configuração de rotas, controladores, modelos e utilitários
      views\               # páginas e configuração da aplicação
      .htaccess            # impede acesso web direto ao core
    public\                # único diretório que deve ser servido pelo Apache
      index.php            # front controller
      css\
      datatables\
      img\
      js\
  project\                 # ficheiros de desenvolvimento; não é necessário em produção
    .vscode\
    salas.code-workspace
    sgreserva.sql          # dump inicial da base de dados
    ...
```

O diretório `app/` é o pacote de distribuição: inclui o código e ficheiros necessários à execução, este README, a licença e o dump SQL. O ficheiro `config.json` contém credenciais específicas do ambiente e é ignorado pelo Git. A árvore inteira `C:\wamp64\dev\salas`, incluindo `app/config.json`, fica fora do `DocumentRoot` geral `C:\wamp64\www`; nos dois modos abaixo, o Apache só publica `app/public/`. Crie o `config.json` a partir de `config.example.json` em cada ambiente e nunca publique credenciais no repositório. Para deployment, copie o conteúdo de `app/` e forneça separadamente o `config.json` seguro do servidor. O repositório Git fica na raiz `C:\wamp64\dev\salas` e versiona `app/` e `project/`; faça as alterações ao código dentro de `app/`. O diretório `project/` contém ficheiros locais/de desenvolvimento e não é necessário em produção. Não coloque a raiz do repositório, `app/core/` nem `project/` sob um `DocumentRoot` público.

## Autenticação e sessão

O núcleo reutilizável da autenticação está em `app/core/app/auth/`: `Authenticator` valida credenciais através do contrato `CredentialRepository`, e `SessionManager` gere a identidade e o ciclo de vida da sessão PHP. Estes componentes não dependem do modelo de utilizador, da base de dados ou das páginas da aplicação.

A integração específica com o sistema de reservas está em `app/core/app/integration/`. `SalasCredentialRepository` liga o contrato de credenciais ao modelo `Usuario`; `SalasSessionAdapter` mantém as chaves de sessão legadas (`user_id`, `user_nome`, `user_nivel` e `ultima_atividade`) para que as páginas, as permissões e as reservas existentes continuem a funcionar. Sessões já iniciadas antes desta separação são aceites e adotadas pelo novo gestor sem exigir novo login. O suporte a hashes MD5 legados e a sua migração após autenticação válida também foi mantido.

Esta separação não inclui a manutenção de perfis nem as permissões por níveis: continuam a pertencer à aplicação. O esquema da base de dados não muda.

## Separação inicial entre controller e view

O fluxo da ação existente `?acao=login` é coordenado por `LoginController`: recebe os dados de pedido do front controller, executa login ou registo e prepara o estado de apresentação. A view `login.php` limita-se a renderizar esse estado. A rota, os campos dos formulários, as mensagens e os redirecionamentos existentes mantêm-se.

Esta é uma intervenção incremental. As restantes páginas ainda têm diferentes graus de lógica de pedido e apresentação misturados; podem ser separadas por fluxos, mantendo inicialmente o router e as URLs atuais.

## Separação da vista principal do dashboard

O front controller mantém o encaminhamento atual para a página inicial, mas agora executa `DashboardPageController` antes de incluir `views/index.php`. O controller interpreta os filtros de data e categoria e prepara um modelo de apresentação com categorias, períodos, salas, reservas e datas de navegação. A view principal limita-se a renderizar esses dados e a escapar os valores apresentados. O `dashboardController` fornece os dados estruturados do mapa de reservas, sem construir HTML para esta página.

## Deployment de uma atualização

1. Teste as alterações em `app/` no ambiente local e guarde-as no Git a partir de `C:\wamp64\dev\salas`.
2. Faça cópia de segurança da versão de produção e do `config.json` de produção. Este ficheiro fica em `app/config.json`, fora do DocumentRoot; não o substitua pelas credenciais locais durante a atualização.
3. Copie a pasta `app/` para o servidor, mantendo a estrutura `app/core/` e `app/public/`. Não copie `project/` nem `.git/`.
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

   `url_base_path: "auto"` deteta automaticamente o prefixo do pedido: vazio na raiz de um VirtualHost e `/salas` quando a aplicação é servida por Alias. Também é possível fixar o valor como `""` ou `"/salas"`, respetivamente. O Apache precisa de permissão de leitura no ficheiro. Mantenha-o fora do DocumentRoot, limite as permissões de leitura à conta do serviço e não o publique nem o inclua em pacotes ou backups acessíveis pela web. Guarde cópias de segurança das credenciais apenas em armazenamento protegido. Não use a conta `root` em produção. `core/views/config.php` lê esse JSON e lança um erro explícito se faltar, estiver inválido ou tiver campos incorretos.
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

Esta opção mantém `C:\wamp64\www` como `DocumentRoot` do servidor principal e não exige criar um VirtualHost para a aplicação. O Alias publica somente `app/public/`; a raiz do repositório, `app/core/`, `app/config.json` e `project/` permanecem fora do espaço HTTP. Com `url_base_path` em `auto`, os links, formulários e recursos são gerados sob `/salas/`.

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

## Verificação após a instalação

- A página inicial abre em `http://salas/` ou `http://salas.test/` (VirtualHost) e em `http://localhost/salas/` (Alias).
- Os estilos, imagens, scripts, formulários e rotas continuam sob o prefixo correto em ambos os modos.
- A aplicação consegue ligar à base de dados `sgreserva`.
- As páginas e operações que leem ou alteram dados funcionam com a conta MySQL da aplicação.
- A raiz do repositório, `app/core/`, `app/config.json` e `project/` não são acessíveis diretamente através do navegador.

O dump disponibiliza o utilizador inicial `teste` com a palavra-passe `teste`. Altere essa palavra-passe ou desative a conta inicial após a primeira entrada e antes de disponibilizar o sistema a utilizadores.
