
//Soluções para problemas de cache do navegador e sessão expirada (Ajax - Jquery) - *** INICIO ***

var appBasePath = new URL(document.currentScript.src).pathname.replace(/\/js\/lib\.js$/, '');

function appUrl(path) {
    return appBasePath + '/' + String(path || '').replace(/^\/+/, '');
}


// 1. CONFIGURAÇÃO GLOBAL AJAX: Força o browser a consultar SEMPRE o servidor
$.ajaxSetup({
    cache: false, // 🏆 CORREÇÃO CRÍTICA: Impede o navegador de usar o HTML antigo em cache
    headers: {
        'X-Requested-With': 'XMLHttpRequest' // Garante que o PHP reconhece sempre como AJAX
    }
});

// INTERCETOR GLOBAL AJAX: Escuta todas as requisições do sistema
$(document).ajaxError(function(event, jqXHR, ajaxSettings, thrownError) {
    // Se o servidor PHP responder com o status 401 (Sessão Expirada)
    if (jqXHR.status === 401) {
        // Limpa a página inteira e redireciona para a raiz pública de login
        window.location.href = appUrl('');
    }
});

//Soluções para problemas de cache do navegador e sessão expirada (Ajax - Jquery) - *** INICIO ***


function getVisible() {
	// configurar a altura da div para nao dar scroll
	$(".tabelarow").css("height", window.innerHeight - 200);
	$(".form").css("height", window.innerHeight - 30);
}


function abreReserva(o) {
	
	// limpa o CSS das outras células
	$(".corpo td").css("background-color", "");
	$(".corpo td").css("color", "#CCC");
	
	// o item selecionado fica com o mesmo cinza do hover de um slot disponível.
	$(o).css("background-color", "#DFDFDF"); 
	$(o).css("color", "black");

	//abre o formulario por AJAX preenchido
	let abre = true;  

	if (abre) {  

		$.ajax({
			type: "GET",
			// 🎯 CORREÇÃO CRÍTICA: Aponta para a raiz pública e passa a ação do MVC
			url: appUrl('?acao=reserva_form'),
			method: "GET",
			
			data: {
				id: $(o).attr("id"),
				data: data,
				sala_id: $(o).data("sala"),
				periodo_id: $(o).data("periodo"),
				usuario_id: $(o).data("usuario_id")
			},
			dataType: 'html',
			success: function (response) {

				$('.form').show("fast", "", function () {

					$('.form').html(response);

					$('#dia').datetimepicker({
						timepicker: false,
						format: 'd/m/Y'
					});

					$('#data_final').datetimepicker({
						timepicker: false,
						format: 'd/m/Y'
					});

				});
			}

		});
	};
}

function fecharForm() {
	$('.form').hide("fast", "", function () {
		// a tabela de reservas já não estica e encolhe para comodar o form
		// $(".corpo").css("max-width", "calc(100% - 180px)");
		$(".corpo td").css("background-color", "");	
		$(".corpo td").css("color", "black");	
	});
}

// ???
var salvando = false;

function salvaFormularioReserva() {

	// pegar todos os dados do formulario
	if (salvando === true)
		return false;

	// validação de campos obrigatórios
	var entidade = ($('#entidade').val() || '').trim();
	var usoId = $('#uso_id').val();
	if (entidade === '') {
		alert('Preencha a entidade.');
		return false;
	}
	if (!usoId || usoId === '0' || usoId === '') {
		alert('Selecione um uso.');
		return false;
	}

	salvando = true;

	$.ajax({
		type: "POST",
		// 🎯 CORREÇÃO 1: Envia para a rota central pública
		url: appUrl('?acao=reserva_form'),
		method: "POST",
		// Junta um parâmetro extra 'submeter_salvar=1' para o controlador saber que é uma gravação
		data: $("#frm_reserva").serialize() + "&submeter_salvar=1",
		success: function (response) {

			// recarregar pagina no dia atual.
			if ($.isNumeric(response))
				// 🎯 CORREÇÃO 2: Redireciona através da raiz limpa "/" do MVC
				window.location.href = appUrl('?data=') + data_pt;
			else {
				alert(response);
				salvando = false;
			}
		}
	});

	return false;

}


function excluirFormularioReserva(id) {

	if (confirm("Excluir reserva?")) {

		$.ajax({
			type: "POST",
			// 🎯 CORREÇÃO 3: Unifica e envia também para a rota do reserva_form
			url: appUrl('?acao=reserva_form'),
			method: "POST",
			// Passa o ID e um gatilho 'submeter_excluir=1' para o controlador interceptar
			data: {
				id: id,
				submeter_excluir: 1,
				csrf_token: (document.querySelector('input[name="csrf_token"]') || {}).value || ''
			},
			success: function (response) {
				// recarregar pagina no dia atual.
				if ($.isNumeric(response))
					// 🎯 CORREÇÃO 4: Redireciona através da raiz limpa "/" do MVC
					window.location.href = appUrl('?data=') + data_pt;
				else
					alert(response)
			}
		});

	}
}

function abre(url) {
    // 🎯 MELHORIA: Verifica se o texto CONTÉM '.php' em vez de apenas terminar com ele
    if (url.includes('.php') && !url.includes('?acao=')) {
        
        // Separa os parâmetros caso existam (ex: categoria_form.php?id=5)
        var partes = url.split('.php');
        var nomeFicheiro = partes[0]; // 'categoria_form'
        var parametrosExtra = partes[1]; // '?id=5' ou vazio ''
        
        // Monta a nova rota limpa
        var novaUrl = appUrl('?acao=' + nomeFicheiro);
        
        // Se houver parâmetros extra, junta-os usando '&' em vez de '?'
        if (parametrosExtra) {
            novaUrl += parametrosExtra.replace('?', '&');
        }
        
        window.location.href = novaUrl;
    } else {
        window.location.href = url;
    }
}



function show_salvar() {
	document.getElementById("salvar").style.display = "inline-block";
}

function cancelaInputsRequired() {        
	$('input').prop('value','');
	$('input').prop('required', false);		
}

function printDiv() {
	var divToPrint = document.getElementsByClassName('print')[0];
	var anotherWindow = window.open('', 'Print-Window');
	anotherWindow.document.open();
	anotherWindow.document.write('<html><body onload="window.print()">' + divToPrint.innerHTML + '</body></html>');
	anotherWindow.document.close();
	setTimeout(function() {
	   anotherWindow.close();
	}, 10);
 }
 
 function plainPrint() {
	$(".menu").hide(); 
	window.print();
	$(".menu").show();
  }
