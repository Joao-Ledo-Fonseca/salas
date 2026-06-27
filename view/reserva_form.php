<?php

define('REQUIRED_PERMISSION', 'M_Reservas');
require_once "seguranca.php";
require_once '../controller/reservaController.php';
require_once '../controller/periodoController.php';
require_once '../controller/salaController.php';

$reserva = new ReservaController();
$periodo = new PeriodoController();
$sala    = new SalaController();

// salvar dados
$reserva->salvarController();

if (isset($_GET['id']) && is_numeric($_GET['id'])) {

	$reg_id = $_GET['id'];
	$sala_id = isset($_GET['sala_id']) && is_numeric($_GET['sala_id']) ? $_GET['sala_id'] : 0;
	$periodo_id = isset($_GET['periodo_id']) && is_numeric($_GET['periodo_id']) ? $_GET['periodo_id'] : 0;
	$usuario_id = isset($_GET['usuario_id']) && is_numeric($_GET['usuario_id']) ? $_GET['usuario_id'] : 0;

	if ($reg_id > 0) {
		$row = $reserva->abrirController($reg_id);
		extract($row[0]);
	} else {
		$status = 1; // reservado	

		// sugerir data do calendario atual
		if (isset($_GET['data'])) {
			$hoje = date_create_from_format('d/m/Y', $_GET['data']);
			if ($hoje === false) {
				$hoje = new DateTime();
			}
		} else {
			$hoje = new DateTime();
		}

		$dia = $hoje->format("d/m/Y");
		$professor_desc = '';
		$disciplina_desc = '';
		$observacao = '';
		$periodo = $periodo_id ? $periodo->nomePeriodo($periodo_id) : '';
		$sala = $sala_id ? $sala->nomeSala($sala_id) : '';
		$usuario = $_SESSION['user_nome'];		
	}


	
	// Verificar que funciona
	// 1 reservada, 2 confirmada, 3 cancelada 
   	$options_status = $reserva->options_status($status);
	?>


	<form name="frm_reserva" id="frm_reserva" onsubmit="return salvaFormularioReserva()">

		<h3> Reserva</h3>

		<input type="hidden" name="id" value="<?= $reg_id ?>" />
		<!-- 
		<input type="hidden" name="sala" value="<?= $sala ?>" />
		<input type="hidden" name="periodo" value="<?= $periodo ?>" />		
		-->

		<input type="hidden" name="sala_id" value="<?= $sala_id ?>" />
		<input type="hidden" name="periodo_id" value="<?= $periodo_id ?>" />


		<input type="text" placeholder="dia" name="dia" id="dia" value="<?= $dia ?>"><BR />
		<!-- eram hidden --> 
		<input type="text" name="sala" value="<?= $sala ?>" disabled />
		<input type="text" name="periodo" value="<?= $periodo ?>" disabled />
		<!-- eram hidden -->
		<input type="text" placeholder="Entidade" name="professor" id="professor" value="<?= $professor_desc ?>"><BR />

		<input type="text" placeholder="Uso" name="disciplina" id="disciplina" value="<?= $disciplina_desc ?>"><BR />

		<input type="text" placeholder="Observação" name="observacao" id="observacao" value="<?= $observacao ?>"><BR />
<br />
		<select name="status" id="status"><?= $options_status; ?></select><BR>
		

		<h3> Repetir semanalmente até</h3>
		<input type="text" placeholder="data final" name="data_final" id="data_final" value=""><BR />

		<input type="button" name="Fechar"  value="Fechar"  class="btn1" style="width:80px" onClick="fecharForm()" >
		<input type="button" name="Excluir" value="Excluir" class="btn1" style="width:80px" onclick="excluirFormularioReserva(<?= $reg_id ?>)">
		<input type="submit" name="salvar"  value="Salvar"  class="btn1" style="width:80px">
		<br><br>
		<Label for="user" >Registado por: </Label>
		<!-- Registado por: <span class="input"><?= $usuario ?></span> -->
		<input type="text" placeholder="User" name="user" id="user" value="<?= $usuario ?>" style="width:50%" disabled><BR />
		
	</form>
	<?php

}

?>