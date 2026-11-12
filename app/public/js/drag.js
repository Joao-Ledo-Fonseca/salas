// === Drag horizontal para reservar múltiplos períodos ===
var dragStartCell = null;
var isDragging = false;
var dragCells = [];
var dragMoved = false;

$(document).on('mousedown', '.tabelarow td.disponivel', function (e) {
    e.preventDefault();
    dragStartCell = this;
    isDragging = true;
    dragMoved = false;
    dragCells = [this];
    $(this).addClass('selected');
});

$(document).on('mousemove', '.tabelarow td', function (e) {
    if (!isDragging || !dragStartCell) return;
    var $row = $(dragStartCell).closest('tr');
    if (!$(this).closest('tr').is($row)) return;
    var cells = $row.find('td');
    var startIdx = cells.index(dragStartCell);
    var curIdx = cells.index(this);
    if (curIdx < 0) return;
    var lo = Math.min(startIdx, curIdx);
    var hi = Math.max(startIdx, curIdx);
    // reset previous selection in this row
    cells.each(function () {
        $(this).removeClass('selected');
    });
    dragCells = [];
    for (var i = lo; i <= hi; i++) {
        var c = cells[i];
        if (!$(c).hasClass('disponivel')) {
            // stop extending past an occupied cell
            break;
        }
        dragCells.push(c);
    }
    // color all selected cells
    dragCells.forEach(function (c) {
        $(c).addClass('selected');
    });
    if (dragCells.length > 1) dragMoved = true;
});

$(document).on('mouseup', function (e) {
    if (!isDragging) return;
    isDragging = false;
    if (dragMoved && dragCells.length > 1) {
        var periodos = dragCells.map(function (c) { return $(c).data('periodo'); }).join(',');
        var first = dragCells[0];
        var selected = dragCells.slice();
        abrirFormMulti(first, periodos, selected);
    }
    dragStartCell = null;
    dragCells = [];
});

function abrirFormMulti(o, periodos, selected) {
    $('.corpo td').removeClass('selected');
    if (selected && selected.length) {
        selected.forEach(function (c) {
            $(c).addClass('selected');
        });
    } else {
        $(o).addClass('selected');
    }

    $.ajax({
        type: 'GET',
        url: appUrl('?acao=reserva_form'),
        method: 'GET',
        data: {
            id: $(o).attr('id'),
            data: data,
            sala_id: $(o).data('sala'),
            periodo_id: $(o).data('periodo'),
            periodos: periodos,
            usuario_id: $(o).data('usuario_id')
        },
        dataType: 'html',
        success: function (response) {
            $('.form').show('fast', '', function () {
                $('.form').html(response);
                $('#dia').datetimepicker({ timepicker: false, format: 'd/m/Y' });
                $('#data_final').datetimepicker({ timepicker: false, format: 'd/m/Y' });
                $('#data_final_diario').datetimepicker({ timepicker: false, format: 'd/m/Y' });
            });
            // manter colorido após abrir o form
            if (selected && selected.length) {
                selected.forEach(function (c) {
                    $(c).addClass('selected');
                });
            }
        }
    });
}

// Suprimir click nativo se houve drag
$(document).on('click', '.tabelarow td', function (e) {
    if (dragMoved) {
        e.stopImmediatePropagation();
        e.preventDefault();
        dragMoved = false;
    }
}, true);
