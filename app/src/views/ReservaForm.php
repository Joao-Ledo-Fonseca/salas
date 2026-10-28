	<form name="frm_reserva" id="frm_reserva" onsubmit="return salvaFormularioReserva()">

		<h3> Reserva</h3>

		<input type="hidden" name="id" value="<?= (int) $id ?>" />
		<!-- 
		<input type="hidden" name="sala" value="<?= $sala ?>" />
		<input type="hidden" name="periodo" value="<?= $periodo ?>" />		
		-->

		<input type="hidden" name="sala_id" value="<?= (int) $sala_id ?>" />
		<input type="hidden" name="periodo_id" value="<?= (int) $periodo_id ?>" />


		<input type="text" placeholder="dia" name="dia" id="dia" value="<?= $dia ?>"><BR />
		<!-- eram hidden --> 
		<input type="text" name="sala" value="<?= $sala ?>" disabled />
		<input type="text" name="periodo" value="<?= $periodo ?>" disabled />
		<!-- eram hidden -->
		<input type="text" placeholder="Entidade" name="professor" id="professor" value="<?= $professor_desc ?>"><BR />

		<input type="text" placeholder="Uso" name="disciplina" id="disciplina" value="<?= $disciplina_desc ?>"><BR />

		<input type="text" placeholder="Observação" name="observacao" id="observacao" value="<?= $observacao ?>"><BR />
<br />
		<select name="status" id="status">
			<?php foreach ($options_status as $option): ?>
				<option value="<?= (int) $option['value'] ?>" <?= $option['selected'] ? 'selected' : '' ?>><?= htmlspecialchars($option['nome'], ENT_QUOTES, 'UTF-8') ?></option>
			<?php endforeach; ?>
		</select><BR>
		

		<h3> Repetir semanalmente até</h3>
		<input type="text" placeholder="data final" name="data_final" id="data_final" value=""><BR />

		<input type="button" name="Fechar"  value="Fechar"  class="btn1" style="width:80px" onClick="fecharForm()" >
		<input type="button" name="Excluir" value="Excluir" class="btn1" style="width:80px" onclick="excluirFormularioReserva(<?= (int) $id ?>)">
		<input type="submit" name="salvar"  value="Salvar"  class="btn1" style="width:80px">
		<br><br>
		<Label for="user" >Registado por: </Label>
		<!-- Registado por: <span class="input"><?= $usuario ?></span> -->
		<input type="text" placeholder="User" name="user" id="user" value="<?= $usuario ?>" style="width:50%" disabled><BR />
		
	</form>
