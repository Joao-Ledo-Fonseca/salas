# Guia de Arquitetura: Refatoramento do Sistema de Temas e Color Picker

## 1. Contexto e Objetivo

Refatorar a camada de interface (UI) e gestão de eventos do editor de temas numa aplicação **PHP + Vanilla JS**, em estrita conformidade com as normas não conflituantes e o mapeamento autoritário definidos no documento base **`ARQUITETURA_TEMAS.md`**.

A aplicação já possui:

- **Fonte Autoritária de Mapeamento:** O catálogo oficial de variáveis CSS (`:root`), a sua correspondência exata com os campos/inputs de ecrã e as respetivas chaves/labels no ficheiro **JSON** já estão definidos no código e transcritos em `ARQUITETURA_TEMAS.md`. **Esta estrutura e nomenclatura devem manter-se intactas.**
- Suporte integral a **CSS Custom Properties** injetadas em `:root` (`document.documentElement`).
- Persistência de temas em ficheiro **JSON** gerido via PHP.
- Formulários em PHP organizados por **abas** (**Base**, **Gerais (cores gerais da aplicação)**, **Menu**, **Dashboard**, **Avisos**).
- Algoritmo de geração automática de paletas (**HSL / OKLCH**) a partir de uma **Cor Base**.

### Decisão Arquitetural: Abandono do Vanilla Picker e Simplificação da UI

- **Abandono e Limpeza Total:** Fica expressamente definido que **se abandona a atual implementação de edição de cores baseada no Vanilla Picker** (e/ou `<input type="color">` nativo). **Todas as referências, dependências, scripts, instâncias, estilos e atributos HTML associados ao Vanilla Picker devem ser completamente eliminados do projeto.**
- **Preservação do Mapeamento Autoritário:** Nenhuma variável CSS, atributo de ligação ao JSON ou estrutura de dados descrita em `ARQUITETURA_TEMAS.md` pode ser renomeada ou alterada.
- **Simplificação da Camada de UI:** O código JavaScript da interface de edição deve ser drasticamente simplificado e enxuto, removendo wrappers complexos e listeners redundantes, mantendo total compatibilidade e isolamento face às regras estabelecidas em `ARQUITETURA_TEMAS.md`.

---

## 2. Stack e Ferramentas Escolhidas

- **Color Picker:** `@melloware/coloris` (via CDN ou ficheiro estático local).
  - *Motivo:* Instância única global renderizada no final do `<body>` (evita cortes de `overflow: hidden` nas abas), zero dependências, controlo explícito via callbacks e atualização visual programática sem loops de eventos.
- **Gestão de Estado:** JavaScript ES6+ minimalista e encapsulado.
- **Backend:** PHP existente (leitura/escrita de JSON).

---

## 3. Regras de Arquitetura e Isolamento (Obrigatório)

1. **Respeito pela Fonte Autoritária (`ARQUITETURA_TEMAS.md`):** Manter rigorosamente os nomes das variáveis CSS, os `name`/`id`/`data-*` dos inputs e o esquema de chaves/labels JSON já definidos no código e documentados em `ARQUITETURA_TEMAS.md`.
2. **Eliminação de Legado (Vanilla Picker / Native Picker):** Remover todos os imports, ficheiros CSS/JS e instâncias do `vanilla-picker` (ou `<input type="color">`). Todos os seletores de cor passam a ser `<input type="text">` simples ligados ao **Coloris**.
3. **Simplificação e Zero Listeners Globais:** Eliminar listeners globais de `change`, `input`, `blur` ou `focusout` no `<form>` que causavam conflitos com as abas. Toda a interação manual do utilizador passa exclusivamente pelo callback único `onChange` do **Coloris**.
4. **Blindagem do Popup (`#clr-picker`):** O contentor global do Coloris deve ter `z-index: 999999` e bloquear a propagação (`e.stopPropagation()`) na fase de captura (`true`) para eventos de clique/foco, impedindo que os scripts das abas fechem ou interfiram com o seletor.
5. **Fonte Única de Verdade (Single Source of Truth):** O estado ativo/rascunho reside num objeto JS simples em memória, sincronizando o `:root` e os `<input>` das abas numa única direção (evitando loops infinitos).

---

## 4. Contrato de HTML Simplificado (Formulários PHP nas Abas)

Nos templates PHP das abas (**Base**, **Gerais (cores gerais da aplicação)**, **Menu**, **Dashboard**, **Avisos**), **manter os identificadores, nomes e atributos de mapeamento CSS/JSON autoritários já existentes** (conforme `ARQUITETURA_TEMAS.md`), alterando apenas o tipo de input para `text`, adicionando a classe de ligação do Coloris (ex: `.js-color-token`) e removendo marcas do Vanilla Picker:

```html
<!-- Exemplo ilustrativo: Manter os atributos/nomes autoritários existentes no código -->
<input 
  type="text" 
  class="js-color-token" 
  <!-- Manter id, name e data-* originais que ligam à variável CSS e chave JSON -->
  value="#0055ff" 
  autocomplete="off"
/>
```

---

## 5. Fluxo de Dados e Métodos Principais (UI Simplificada)

O controlador de UI (`ThemeEditorUI.js`) deve ser reduzido ao essencial, consumindo diretamente o mapeamento autoritário existente no projeto:

### A. Inicialização e Blindagem

- Carregar o JSON do tema ativo e guardar uma cópia imutável em `initialThemeTokens` (para suportar a função de Reset).
- Inicializar o `Coloris` apontando para os inputs de cor.
- Aplicar `stopPropagation()` no elemento `#clr-picker` para os eventos `['mousedown', 'pointerdown', 'click', 'focusin']`.

### B. Fluxo 1: Alteração da Cor Base (Cascata)

1. Utilizador altera o campo da **Cor Base** no Coloris -> dispara `onChange(color, inputEl)`.
2. O sistema identifica o gatilho da cor base (conforme mapeamento autoritário).
3. Executa o algoritmo existente (`HSL / OKLCH`) que devolve o mapa de variáveis CSS atualizado.
4. Chama `applyTokensToUIAndRoot(paletteMap)` que:
   - Injeta cada variável em `document.documentElement.style.setProperty(varName, hex)`.
   - Localiza cada input correspondente através do mapeamento autoritário existente e atualiza `input.value = hex` (mesmo em abas ocultas).
   - Dispara `input.dispatchEvent(new Event('input', { bubbles: true }))` apenas para atualizar a miniatura visual de cor do Coloris (sem acionar o `onChange`).

### C. Fluxo 2: Edição Manual de Token Individual

1. Utilizador altera qualquer outro campo de cor -> dispara `onChange(color, inputEl)`.
2. Resolve a variável CSS correspondente através do mapeamento autoritário existente.
3. Executa `document.documentElement.style.setProperty(cssVar, color)` para preview imediato.

### D. Fluxo 3: Repor Valores Iniciais (Reset)

1. Utilizador clica em "Repor Tema".
2. Chama `applyTokensToUIAndRoot(this.initialThemeTokens)`.
3. Todas as variáveis no `:root`, valores dos inputs e amostras visuais do Coloris regressam ao estado original do JSON sem recarregar a página.

### E. Fluxo 4: Gravar / Criar Novo Tema

1. Lê os valores atuais dos inputs usando o mapeamento autoritário existente para montar o payload JSON no formato oficial da aplicação.
2. Envia o payload via `fetch()` (POST) para o endpoint PHP existente.
3. Atualiza `this.initialThemeTokens` com os novos valores gravados.

---

## 6. Tarefas de Execução para o Cline (Checklist)

- [ ] **Passo 1 (Leitura da Fonte Autoritária):** Ler o ficheiro `ARQUITETURA_TEMAS.md` e localizar no código a fonte autoritária que mapeia variáveis CSS, inputs de ecrã e labels/chaves JSON. Garantir que este contrato permanece 100% inalterado.
- [ ] **Passo 2 (Limpeza Total do Vanilla Picker):** Pesquisar em todo o projeto (PHP, JS, CSS) e **eliminar todas as referências, ficheiros, imports, instâncias e estilos do `Vanilla Picker`** (e inputs nativos `type="color"`).
- [ ] **Passo 3 (Templates PHP):** Simplificar o HTML das abas do editor de temas (**Base**, **Gerais**, **Menu**, **Dashboard**, **Avisos**) para usar `<input type="text">` compatíveis com o Coloris, preservando intactos os atributos/nomes de mapeamento autoritário.
- [ ] **Passo 4 (Integração Coloris):** Incluir o CSS e JS do `Coloris` no layout principal da página de temas (antes do script do editor).
- [ ] **Passo 5 (Refatoramento JS Simplificado):** Reescrever/simplificar o ficheiro JS da UI do editor de temas, removendo código complexo e listeners antigos, ligando o algoritmo HSL/OKLCH e o mapeamento autoritário existente ao fluxo do Coloris.
- [ ] **Passo 6 (Validação):** Testar os fluxos de **Cor Base -> Paleta**, **Edição Individual**, **Preview**, **Reset (Repor JSON)**, **Guardar Existente** e **Criar Novo Tema** em todas as abas.
- [ ] **Passo 7 (Encerramento e Documentação):** Após aprovação e validação funcional, **atualizar a documentação do projeto (incluindo o próprio `ARQUITETURA_TEMAS.md` e `README.md`)** para remover qualquer menção ao Vanilla Picker e refletir a nova arquitetura simplificada baseada no `Coloris`, mantendo a tabela/mapeamento autoritário de variáveis CSS e JSON intacta.
