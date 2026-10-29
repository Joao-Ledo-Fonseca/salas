# Inventário de rotas e permissões

Gerado na Fase 0 da refatoração MVC.

## Rotas tratadas diretamente no front controller

| Ação | Método | Descrição |
|---|---|---|
| `login` | GET/POST | Autenticação e registo |
| `logout` | GET | Termina a sessão |
| *(sem ação)* | GET | Dashboard inicial |

## Rotas mapeadas em `src/config/routes.php`

| Ação | Vista | Permissão |
|---|---|---|
| `categoria_list` | `categoria_list.php` | `M_Categorias` |
| `categoria_form` | `categoria_form.php` | `M_Categorias` |
| `sala_list` | `sala_list.php` | `M_Salas` |
| `sala_form` | `sala_form.php` | `M_Salas` |
| `periodo_list` | `periodo_list.php` | `M_Periodos` |
| `periodo_form` | `periodo_form.php` | `M_Periodos` |
| `usuario_list` | `usuario_list.php` | `M_Utilizadores` |
| `usuario_form` | `usuario_form.php` | próprio perfil ou `M_Utilizadores` |
| `permissoes_form` | `permissoes_form.php` | `M_Permissoes` |
| `estatisticas` | `Estatisticas.php` | `M_Estatisticas` |
| `relatorios` | `Relatorios.php` | `M_Relatorios` |
| `reserva_form` | `reserva_form.php` | `M_Reservas` |

## Operações AJAX

| Ação | POST | Resposta |
|---|---|---|
| `reserva_form` com `submeter_salvar` | guardar reserva | texto/ID |
| `reserva_form` com `submeter_excluir` | excluir reserva | texto/ID |

## Checklist de smoke tests

- [ ] Login com utilizador válido e inválido.
- [ ] Registo de novo utilizador a partir do login.
- [ ] Logout.
- [ ] Sessão expirada em navegação normal e AJAX.
- [ ] Dashboard sem categoria e com categoria.
- [ ] Navegação entre dias com e sem reservas.
- [ ] CRUD de categorias, incluindo imagem.
- [ ] CRUD de períodos.
- [ ] CRUD de salas, incluindo filtros e imagem.
- [ ] CRUD de utilizadores e edição do próprio perfil.
- [ ] Formulário de permissões por nível.
- [ ] Reserva: criar, editar, excluir e agendamento repetido.
- [ ] Relatórios por mês, semana e período.
- [ ] Acesso negado para utilizador sem permissão.
- [ ] Menu visível conforme nível.


