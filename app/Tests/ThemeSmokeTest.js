// Smoke Test para Funcionalidades de Temas
function runThemeSmokeTest() {
    console.log("Iniciando testes de fumo...");

    // 1. Testar Abas
    var tabs = document.querySelectorAll('.tema-tab');
    if (tabs.length === 0) console.error("Falha: Abas não encontradas.");
    else {
        showTab('base');
        if (document.getElementById('tab-base').classList.contains('active')) console.log("Sucesso: Aba 'Base' ativa.");
        else console.error("Falha: Aba 'Base' não ativou.");
    }

    // 2. Testar Color Picker
    var pickers = document.querySelectorAll('.color-picker-btn');
    if (pickers.length === 0) console.error("Falha: Color Pickers não encontrados.");
    else console.log("Sucesso: " + pickers.length + " Color Pickers encontrados.");

    // 3. Testar Seleção de Tema
    var editSelect = document.getElementById('edit_theme');
    if (!editSelect) console.error("Falha: Seletor de tema para editar não encontrado.");
    else console.log("Sucesso: Seletor de tema encontrado.");

    console.log("Testes de fumo concluídos.");
}

// Executar após carregamento
document.addEventListener('DOMContentLoaded', runThemeSmokeTest);
