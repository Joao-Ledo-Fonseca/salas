

function getVisible() {
	// configurar a altura da div para nao dar scroll
	$(".tabelarow").css("height", window.innerHeight - 120);
	$(".form").css("height", window.innerHeight - 30);
}


function abreReserva(o) {
	
	// limpa o CSS das outras células
	$(".corpo td").css("background-color", "");
	$(".corpo td").css("color", "#CCC");
	
	// o item selecionado fica verde.
	//$(o).css("background-color", "#CAF4D5");
	$(o).css("background-color", "#BFF9B9"); 
	$(o).css("color", "black");

	
	
	//abre o formulario por AJAX preenchido
	let abre = true;

	if (abre) {

		$.ajax({
			type: "GET",
			url: "reserva_form.php",
			method: "GET",
			
			data: "id=" + $(o).attr("id") + "&data=" + data + "&sala_id=" + $(o).attr("sala") + "&periodo_id=" + $(o).attr("periodo") + "&usuario_id=" + $(o).attr("usuario_id"),
			dataType: 'html',
			success: function (response) {

				// a tabela de reservas já não estica e encolhe para acomodar o form
				// $(".corpo").css("max-width", "calc(100% - 510px)"); 

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

	salvando = true;

	$.ajax({
		type: "POST",
		url: 'reserva_form.php',
		method: "POST",
		data: $("#frm_reserva").serialize(),
		success: function (response) {

			// recarregar pagina no dia atual.
			if ($.isNumeric(response))
				window.location.href = "index.php?data=" + data_pt;
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
			url: 'reserva_excluir.php',
			method: "POST",
			data: "id=" + id,
			success: function (response) {
				// recarregar pagina no dia atual.
				if ($.isNumeric(response))
					window.location.href = "index.php?data=" + data_pt;
				else
					alert(response)
			}
		});

	}
}

function abre(url) {
	window.location.href = url;
}



function show_salvar() {
	document.getElementById("salvar").style.display = "inline-block";
}

function cancelaInputsRequired() {        
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


