# Documento Autoritário: Arquitetura de Temas do Projeto Salas

Este documento estabelece a arquitetura autoritária para a gestão de temas no projeto Salas (PHP MVC).

---

## 1. Visão Geral da Arquitetura

O sistema de temas é baseado em ficheiros JSON que definem propriedades visuais (cores, fontes, tamanhos). Estes ficheiros são carregados pelo servidor, processados pela classe `ThemeColorManager` e injetados como variáveis CSS (`--variável`) no elemento `:root` da aplicação, permitindo uma alteração dinâmica e consistente de toda a interface.

## 2. Definição de Temas (JSON)

Os temas residem em `app/themes/`. Cada ficheiro JSON segue a estrutura (exemplo do tema `light.json`):

```json
{
    "name": "Claro",
    "fontFamily": "'Inter', sans-serif",
    "fontSizeBase": "12",
    "baseColor": "#C4E4FF",
    "colors": {
        "bg": "#C4E4FF",
        "surface": "#F4F5F6",
        "text": "#171A1F",
        "textStrong": "#171A1F",
        "textMuted": "#5E6468",
        "border": "#C0C7CE",
        "primary": "#005EAD",
        "primaryDark": "#004884",
        "primaryLight": "#A0CAEE",
        "primaryLink": "#0063B6",
        "overlay": "#9FB2D1",
        "surfaceAlt": "#E2E6E9",
        "borderStrong": "#929FAA",
        "success": "#DBF0E0",
        "warning": "#F0EBDB",
        "danger": "#F0DBDD",
        "dangerDark": "#C5202E",
        "dangerLight": "#F6EFEF",
        "dangerBorder": "#D39298",
        "menuBg": "#1F2428",
        "menuItem": "#343B42",
        "menuHoverBg": "#C0C7CE",
        "menuHoverText": "#171A1F",
        "menuText": "#F4F6F8"
    },
    "dashboard": {
        "reservado": "#F0EBDB",
        "confirmado": "#DBF0E0",
        "cancelado": "#F0DBDD",
        "disponivel": "#889BAE",
        "disponivelHover": "#E4E5E7",
        "selected": "#DBF0E0",
        "cellBorder": "#C5C7C9",
        "cellText": "#171A1F"
    }
}
```

## 3. Mapeamento de Cores (Autoridade)

A classe `Salas\Core\Theme\ThemeColorManager` é a única autoridade para converter chaves JSON em variáveis CSS. O mapeamento exaustivo é definido abaixo:

### 3.1. Cores Gerais (`colors`)

| Chave JSON | Variável CSS | Nome no Ecrã | Uso |
| :--- | :--- | :--- | :--- |
| `bg` | `--color-bg` | Fundo da página | body background |
| `surface` | `--color-surface` | Fundo de superfícies/cartões | .info-box, inputs, selects |
| `surfaceAlt` | `--color-surface-alt` | Fundo alternativo | tabelas secundárias |
| `text` | `--color-text` | Texto base | body color |
| `textStrong` | `--color-text-strong` | Texto forte/títulos | table td, .info-box |
| `textMuted` | `--color-text-muted` | Texto secundário | .text-muted, footer |
| `border` | `--color-border` | Bordas | inputs, selects, .date-input |
| `borderStrong` | `--color-border-strong` | Bordas fortes | bordas com destaque |
| `primary` | `--color-primary` | Cor primária | links, botões, .link |
| `primaryDark` | `--color-primary-dark` | Primária escura (hover) | hover de botões |
| `primaryLight` | `--color-primary-light` | Primária clara | focus de inputs |
| `primaryLink` | `--color-primary-link` | Hiperligações | table a, .link |
| `success` | `--color-success` | Sucesso | .btn2 |
| `warning` | `--color-warning` | Aviso | fallback --dashboard-reservado |
| `danger` | `--color-danger` | Erro/perigo | .eliminar-imagem background |
| `dangerDark` | `--color-danger-dark` | Erro escuro | .form-error, .text-error |
| `dangerLight` | `--color-danger-light` | Erro claro | .form-error background |
| `dangerBorder` | `--color-danger-border` | Borda de erro | .form-error border |
| `overlay` | `--color-overlay` | Overlay/modal | overlay/modal |
| `menuBg` | `--color-menu-bg` | Fundo do menu | .menu background |
| `menuItem` | `--color-menu-item` | Fundo de Item do menu | .menu li, .menu a |
| `menuHoverBg` | `--color-menu-hover-bg` | Fundo hover menu | .menu li:hover |
| `menuHoverText` | `--color-menu-hover-text` | Texto hover menu | .menu li:hover |
| `menuText` | `--color-menu-text` | Texto do menu | .menu, .menu a |

### 3.2. Cores do Dashboard (`dashboard`)

| Chave JSON | Variável CSS | Nome no Ecrã | Uso |
| :--- | :--- | :--- | :--- |
| `reservado` | `--dashboard-reservado` | Célula reservada | .reservado cell |
| `confirmado` | `--dashboard-confirmado` | Célula confirmada | .confirmado cell |
| `cancelado` | `--dashboard-cancelado` | Célula cancelada | .cancelado cell |
| `disponivel` | `--dashboard-disponivel` | Célula disponível | .disponivel cell |
| `disponivelHover` | `--dashboard-disponivel-hover` | Hover célula disponível | .disponivel:hover |
| `selected` | `--dashboard-selected` | Célula selecionada | .selected cell |
| `cellBorder` | `--dashboard-cell-border` | Borda da célula | table th/td, hr |
| `cellText` | `--dashboard-cell-text` | Texto da célula | table td |

## 4. Seleção de Fontes

A seleção de fontes é gerida pela classe `Salas\Core\Theme\ThemeFontFamily`. O ecrã de temas apresenta uma lista de opções pré-definidas. Ao selecionar uma fonte, o valor correspondente (stack de fontes) é injetado na variável `--font-family-base`.

## 5. Arquitetura de Uso

1. **Carregamento**: `ThemeLoader` lê o ficheiro JSON do tema ativo.
2. **Processamento**: `ThemeColorManager` converte as propriedades para variáveis CSS.
3. **Injeção**: `Theme::toCssVariables()` injeta as variáveis no `:root` via `layout.php`.
4. **Consumo**: O CSS (`estilo.css`) utiliza `var(--nome-variavel, fallback)` para aplicar as cores.

## 6. Interface de Administração (`Temas.php`)

O ecrã de temas é o único ponto de edição, estruturado da seguinte forma:

### 6.1. Componentes e Layout

- **Componentes Comuns**: A interface utiliza componentes modulares (ex: `ThemeColorTable.php`) para renderizar tabelas de cores de forma consistente.
- **Layout das Abas**: O ecrã utiliza um sistema de abas (`.tema-tabs`) para organizar as cores por categorias (Base, Gerais, Avisos, Menu, Dashboard, Regras). O conteúdo de cada aba é um contentor (`.tema-tab-content`) que alterna a visibilidade (`active`).
- **Layout dos Botões**: Os botões de ação (Testar, Repor, Gravar, Criar, Eliminar) estão agrupados num contentor flexível (`.flex gap-10 mt-4`) para garantir alinhamento e espaçamento consistentes.
- **Campos de Input**:
  - **Fora das abas**: Campos de texto/select padrão seguem o estilo definido em `estilo.css`.
  - **Dentro das abas**: Campos de input de cor (`.color-input`) são acompanhados por uma caixa de representação visual (`.color-preview-box`) renderizada via HTML puro, envolta num `.color-preview-wrapper` que detém a borda visível.
  - **Regras Autoritárias da Box de Preview**:
        1. A box de preview (`.color-preview-box`) é criada exclusivamente em HTML/CSS puro.
        2. O JavaScript (incluindo o `Pickr`) **NUNCA** altera diretamente a cor de fundo da box de preview.
        3. **Exceção 1 – Clique**: Um `click` na box (ou no wrapper) apenas aciona a abertura do `Pickr`.
        4. **Exceção 2 – Picker → Input**: O `Pickr`, ao guardar uma cor, altera **exclusivamente** o valor do campo de input de texto associado.
        5. **Exceção 3 – Input → Box**: O campo de input tem um evento `input` que, ao detetar uma alteração de valor válido (hexa), altera **exclusivamente** a cor interna (`background`) da box de preview.
        6. **Exceção 4 – Recalculo da cor base**: O recalculo das restantes cores (via `updatePaletteFromBase`) altera o valor do input da cor base e dispara um evento `input`, que por sua vez atualiza exclusivamente a cor interna da box de preview da cor base.

### 6.2. Funcionalidades

- **Cálculo de Cores**: A lógica de cálculo de cores foi movida para o servidor (`app/src/Core/Theme/ColorCalculator.php`). O formulário solicita o cálculo via AJAX (`?acao=temas&ajax=calculate_palette&baseColor=...`).
- **Regras de Cálculo**: As constantes de cálculo de paleta são geridas através do ficheiro `app/config/theme_rules.json` e podem ser editadas na aba "Regras de Cálculo".
- **Comportamento de Preview**:
  - O formulário de Temas gere as suas cores localmente sem interferir nas cores gerais e no visual da aplicação.
  - **Regra Crítica**: A alteração visual na aplicação (fundo, bordas, etc.) **NÃO** deve ocorrer imediatamente. A aplicação visual só é atualizada ao clicar em "Testar" (sempre) ou "Gravar" (se o tema gravado for o tema ativo).
- **Botões**:
  - **Testar**: Aplica as cores atuais do formulário ao preview da aplicação através da função `aplicarPreview()`.
  - **Gravar**: Persiste as alterações no ficheiro JSON do tema e as regras de cálculo no `theme_rules.json`.
  - **Repor**: Recarrega o tema original.
  - **Criar/Eliminar**: Gestão de ficheiros de temas.

## 7. Seleção de Temas

- **Ecrã de Temas**: O administrador seleciona o tema ativo através do formulário "Tema ativo".
- **Menu Principal**: O utilizador pode alternar entre temas disponíveis através de um seletor no menu lateral, que atualiza a sessão do utilizador e recarrega a aplicação com o novo tema.

## 8. Notas para Refatoramento e Limites de Segurança

Para evitar regressões e degradação de funcionalidades, qualquer refatoramento deve respeitar os seguintes limites:

- **Testes de Regressão**: Qualquer alteração na lógica de cores deve ser validada com os testes existentes (`ThemeSmokeTest.js`, `ThemeLoaderTest.php`) antes de qualquer implementação.
- **Isolamento de Lógica**: Refatoramentos devem ser incrementais e isolados. A lógica de cálculo de cores (atualmente em `temas_base.js`) só deve ser movida para o servidor se for garantida a paridade total de comportamento entre a implementação JS atual e a nova implementação PHP.
- **Validação de Impacto**: Alterações na injeção de variáveis CSS devem ser testadas em todos os módulos (Dashboard, Menu, Formulários) para garantir que não existem efeitos colaterais visuais indesejados.
- **Documentação de Mudança**: Qualquer alteração na estrutura de temas ou na lógica de processamento deve ser documentada neste ficheiro antes da sua aplicação.
- **Princípio de "Não-Interferência"**: O formulário de temas deve permanecer isolado do estado visual da aplicação até que o utilizador confirme a alteração (Testar/Gravar). Qualquer alteração que quebre este princípio é considerada uma falha crítica.
