# Guia de Mapeamento de Cores no Projeto Salas (PHP MVC)

Este documento descreve detalhadamente o fluxo completo de gestão, conversão e aplicação de cores no projeto Salas (PHP MVC), abrangendo o armazenamento em JSON, processamento em PHP, administração na interface, consumo em CSS e uma tabela de referência unificada.

---

## 1. Armazenamento em Ficheiros JSON de Temas

Os temas do sistema são definidos através de ficheiros JSON localizados na diretoria [`app/themes/`](app/themes/light.json). Os ficheiros padrão fornecidos são [`app/themes/light.json`](app/themes/light.json) e [`app/themes/dark.json`](app/themes/dark.json).

Cada ficheiro armazena a estrutura do tema utilizando propriedades em formato `camelCase` agrupadas em secções lógicas:
- **Metadados do tema**: `name`, `fontFamily`, `fontSizeBase`, `baseColor`.
- **`colors`**: Objeto contendo as cores gerais da aplicação e do menu (ex: `bg`, `surface`, `text`, `primary`, `menuBg`, etc.).
- **`dashboard`**: Objeto contendo as cores específicas para as células de agendamento e grelhas do dashboard (ex: `reservado`, `confirmado`, `cancelado`, `disponivel`, `cellBorder`, `cellText`, etc.).

Exemplo em [`app/themes/light.json`](app/themes/light.json:1):
```json
{
    "name": "Claro",
    "fontFamily": "'Inter', sans-serif",
    "fontSizeBase": "12px",
    "baseColor": "#BCD4EB",
    "colors": {
        "bg": "#BCD4EB",
        "surface": "#F4F5F6",
        "text": "#171A1F",
        "primary": "#285886",
        "menuBg": "#1F2428"
    },
    "dashboard": {
        "reservado": "#F0EBDB",
        "confirmado": "#DBF0E0",
        "cancelado": "#F0DBDD",
        "disponivel": "#DBE8F4"
    }
}
```

---

## 2. Conversão de `camelCase` para Variáveis CSS `kebab-case`

A classe [`Salas\Core\Theme\Theme`](app/src/Core/Theme/Theme.php:7) é responsável por carregar a estrutura do tema a partir de um array e convertê-la em variáveis CSS nativas através do método [`toCssVariables()`](app/src/Core/Theme/Theme.php:68).

O método privado [`kebab()`](app/src/Core/Theme/Theme.php:93) utiliza uma expressão regular (`preg_replace('/([a-z])([A-Z])/', '$1-$2', $key)`) para transformar propriedades em `camelCase` (como `textStrong` ou `disponivelHover`) em notação `kebab-case` (`text-strong`, `disponivel-hover`).

Regras de prefixação geradas pelo [`toCssVariables()`](app/src/Core/Theme/Theme.php:68):
- Propriedades de base (`fontFamily`, `fontSizeBase`, `baseColor`) geram respetivamente `--font-family-base`, `--font-size-base`, `--color-base`.
- Cada chave do objeto `colors` gera `--color-[kebab-case]` (ex: `primaryDark` $\rightarrow$ `--color-primary-dark`).
- Cada chave do objeto `dashboard` gera `--dashboard-[kebab-case]` (ex: `disponivelHover` $\rightarrow$ `--dashboard-disponivel-hover`).

---

## 3. Ecrã de Administração de Temas ([`app/src/views/Temas.php`](app/src/views/Temas.php:1))

O ecrã de administração de temas [`app/src/views/Temas.php`](app/src/views/Temas.php:1) permite aos administradores inspecionar, editar e testar as cores dos temas.

Na tabela de inputs de cor, cada campo de formulário possui o atributo `data-var` (ex: `data-var="--color-primary"` ou `data-var="--dashboard-confirmado"`). 
Estes atributos [`data-var`](app/src/views/Temas.php:102) ligam diretamente o input de cor da interface gráfica à respetiva variável CSS injetada no documento via [`javascript`](app/public/js/theme.js:1), permitindo o funcionamento do sistema de pré-visualização instantânea (`aplicarPreview()`).

---

## 4. Consumo no CSS com Fallbacks

O ficheiro [`app/public/css/estilo.css`](app/public/css/estilo.css:1) consome estas variáveis injetadas na raiz do documento (`:root`), fornecendo sempre um valor de fallback seguro caso a variável CSS não esteja definida.

Exemplos de consumo em [`app/public/css/estilo.css`](app/public/css/estilo.css:18):
```css
html {
    font-size: var(--font-size-base, 14px);
}

body {
    font-family: var(--font-family-base, 'Inter', sans-serif);
    color: var(--color-text, #4E4E4E);
    background-color: var(--color-bg, #FFFFFF);
}

.menu {
    background-color: var(--color-menu-bg, #464646);
}
```

---

## 5. Referência Rápida por Secções (Separadores do Ecrã de Temas)

As tabelas seguintes catalogam todas as propriedades de cores suportadas, agrupadas exatamente de acordo com os separadores apresentados no ecrã de administração de temas ([`app/src/views/Temas.php`](app/src/views/Temas.php:1)), incluindo explicitamente o nome da cor tal como é apresentado na interface.

### 5.1. Tema para Edição (Base)
Propriedades base e metadados do tema:

| Chave JSON (`camelCase`) | Nome no Ecrã (`Temas.php`) | Variável CSS Gerada | Descrição Funcional / Utilização |
| :--- | :--- | :--- | :--- |
| `baseColor` | Cor base | `--color-base` | Cor base de referência para geração automática de paleta |

### 5.2. Cores Gerais (App)
Cores gerais aplicadas à interface principal da aplicação:

| Chave JSON (`camelCase`) | Nome no Ecrã (`Temas.php`) | Variável CSS Gerada | Descrição Funcional / Utilização |
| :--- | :--- | :--- | :--- |
| `bg` | `bg` (Fundo da página) | `--color-bg` | Cor de fundo principal da aplicação (`body`) |
| `surface` | `surface` (Fundo de superfícies/cartões) | `--color-surface` | Fundo de superfícies, cartões e contentores |
| `text` | `text` (Texto base) | `--color-text` | Cor do texto principal |
| `textStrong` | `textStrong` (Texto forte/títulos) | `--color-text-strong` | Cor de texto com ênfase (títulos e cabeçalhos) |
| `textMuted` | `textMuted` (Texto secundário) | `--color-text-muted` | Cor de texto secundário/desvanecido |
| `border` | `border` (Bordas) | `--color-border` | Cor padrão para bordas e separadores |
| `primary` | `primary` (Cor primária) | `--color-primary` | Cor primária da marca (botões principais, links ativos) |
| `primaryDark` | `primaryDark` (Primária escura) | `--color-primary-dark` | Variação escura da cor primária (efeito hover) |
| `primaryLight` | `primaryLight` (Primária clara) | `--color-primary-light` | Variação clara da cor primária |
| `primaryLink` | `primaryLink` (Hiperligações) | `--color-primary-link` | Cor específica para hiperligações |
| `overlay` | `overlay` (Overlay/modal) | `--color-overlay` | Cor de sobreposição para modais e fundos escuros |

### 5.3. Cores Gerais (Avisos/Erros)
Cores para alertas, estados e mensagens de erro:

| Chave JSON (`camelCase`) | Nome no Ecrã (`Temas.php`) | Variável CSS Gerada | Descrição Funcional / Utilização |
| :--- | :--- | :--- | :--- |
| `surfaceAlt` | `surfaceAlt` (Fundo alternativo) | `--color-surface-alt` | Fundo secundário/alternativo para tabelas e blocos |
| `borderStrong` | `borderStrong` (Bordas fortes) | `--color-border-strong` | Cor para bordas com maior destaque |
| `success` | `success` (Sucesso) | `--color-success` | Cor indicativa de sucesso / confirmação |
| `warning` | `warning` (Aviso) | `--color-warning` | Cor indicativa de aviso / atenção |
| `danger` | `danger` (Erro/perigo) | `--color-danger` | Cor indicativa de erro / perigo |
| `dangerDark` | `dangerDark` (Erro escuro) | `--color-danger-dark` | Variação escura para erros |
| `dangerLight` | `dangerLight` (Erro claro) | `--color-danger-light` | Variação clara para fundos de erro |
| `dangerBorder` | `dangerBorder` (Borda de erro) | `--color-danger-border` | Cor de borda para elementos de erro |

### 5.4. Cores do Menu
Cores aplicadas ao painel de navegação lateral (menu):

| Chave JSON (`camelCase`) | Nome no Ecrã (`Temas.php`) | Variável CSS Gerada | Descrição Funcional / Utilização |
| :--- | :--- | :--- | :--- |
| `menuBg` | `menuBg` (Fundo do menu) | `--color-menu-bg` | Cor de fundo do painel do menu lateral |
| `menuItem` | `menuItem` (Item do menu) | `--color-menu-item` | Cor de fundo dos itens do menu |
| `menuHoverBg` | `menuHoverBg` (Fundo hover do menu) | `--color-menu-hover-bg` | Cor de fundo ao passar o rato (hover) sobre os itens |
| `menuHoverText` | `menuHoverText` (Texto hover do menu) | `--color-menu-hover-text` | Cor do texto ao passar o rato sobre os itens |
| `menuText` | `menuText` (Texto do menu) | `--color-menu-text` | Cor do texto padrão no menu |

### 5.5. Cores do Dashboard
Cores específicas para o grelhado de reservas e estados do dashboard:

| Chave JSON (`camelCase`) | Nome no Ecrã (`Temas.php`) | Variável CSS Gerada | Descrição Funcional / Utilização |
| :--- | :--- | :--- | :--- |
| `reservado` | `reservado` (Célula reservada) | `--dashboard-reservado` | Cor de célula para estado reservado |
| `confirmado` | `confirmado` (Célula confirmada) | `--dashboard-confirmado` | Cor de célula para estado confirmado |
| `cancelado` | `cancelado` (Célula cancelada) | `--dashboard-cancelado` | Cor de célula para estado cancelado |
| `disponivel` | `disponivel` (Célula disponível) | `--dashboard-disponivel` | Cor de célula para estado disponível |
| `disponivelHover` | `disponivelHover` (Hover célula disponível) | `--dashboard-disponivel-hover` | Cor de célula disponível ao passar o rato |
| `selected` | `selected` (Célula selecionada) | `--dashboard-selected` | Cor de célula selecionada |
| `cellBorder` | `cellBorder` (Borda da célula) | `--dashboard-cell-border` | Cor da borda das células do dashboard |
| `cellText` | `cellText` (Texto da célula) | `--dashboard-cell-text` | Cor do texto no interior das células do dashboard |
