# Testes

Executar os testes unitários:

```powershell
.\vendor\bin\phpunit.bat -c phpunit.xml
```

Executar os testes de integração (requer uma base de dados configurada):

```powershell
.\vendor\bin\phpunit.bat -c phpunit.integration.xml
```

Executar os smoke tests:

```powershell
php app/Tests/SmokeTest.php
```

Os testes de autorização e permissões validam:

- atribuição de nível permitido, rejeição de nível inválido e tentativa de elevação;
- gravação das checkboxes selecionadas e revogação das ausentes;
- propagação de alterações apesar de existir uma cache antiga na sessão;
- ausência de efeitos de inicialização da tabela durante a autorização;
- autorização de rotas mapeadas, bloqueio de rotas sem política e exceção do próprio perfil;
- controlo do link e da submissão de registo público por `A_RegNovoUtilizador`, mensagens de sucesso e nomes/e-mails duplicados;
- inventário das rotas atuais sem avisos por mapeamentos em falta.

Para testes HTTP manuais, usar o servidor PHP embutido:

```powershell
php -S 127.0.0.1:8080 -t app/public
```
