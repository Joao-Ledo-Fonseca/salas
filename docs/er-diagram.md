# Diagrama ER — Base de dados sgreserva

Representação em Mermaid das tabelas e relações da aplicação.

```mermaid
erDiagram
    CATEGORIA {
        int id PK
        varchar nome
        varchar descricao
        int imagem_id FK
    }
    IMAGEM {
        int id PK
        varchar nome_imagem
        varchar tamanho_imagem
        varchar tipo_imagem
        longblob imagem
    }
    PERIODO {
        int id PK
        varchar nome
        int seq
    }
    SALA {
        int id PK
        varchar nome
        varchar descricao
        int lugares
        tinyint activa
        int categoria_id FK
        int imagem_id FK
    }
    USUARIO {
        int id PK
        varchar nome
        varchar email
        varchar senha
        varchar telefone
        varchar NIF
        tinyint nivel
    }
    PERMISSOES {
        int seq
        varchar nome PK
        char tipo
        tinyint uexterno
        tinyint uinterno
        tinyint admin
    }
    USO {
        int id PK
        varchar nome
        varchar descricao
    }
    RESERVA {
        int id PK
        int sala_id FK
        int periodo_id FK
        date dia
        varchar entidade_desc
        int uso_id FK
        int status
        text observacao
        int usuario_id FK
        int uso_id FK
    }

    CATEGORIA ||--o{ SALA : "tem"
    CATEGORIA }o--o| IMAGEM : "imagem"
    SALA }o--o| IMAGEM : "imagem"
    SALA }o--|| CATEGORIA : "pertence a"
    RESERVA }o--|| SALA : "ocupa"
    RESERVA }o--|| PERIODO : "no período"
    RESERVA }o--|| USUARIO : "feita por"
    RESERVA }o--o| USO : "uso"
```

## Notas
- `PERMISSOES` não tem FK para utilizador; as permissões são por nível (0=User, 1=Gestor, 2=Admin, 3=SuperAdmin).
- `USO` é referenciado por `RESERVA.uso_id` (ON DELETE SET NULL).
- `IMAGEM` é partilhada por `CATEGORIA` e `SALA` (ON DELETE SET NULL).
