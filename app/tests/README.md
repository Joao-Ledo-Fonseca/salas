# Testes

Executar os smoke tests:

```powershell
php app/tests/SmokeTest.php
```

Os testes validam:

- ligação à base de dados;
- `AuthorizationService`;
- `CategoryService`;
- `SalaService`;
- `PeriodoService`;
- `AuthService` (credenciais inválidas);
- permissões por nível de utilizador.

Para testes HTTP manuais, usar o servidor PHP embutido:

```powershell
php -S 127.0.0.1:8080 -t app/public
```
