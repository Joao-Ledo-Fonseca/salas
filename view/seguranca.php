<?php

session_start();

if(!isset($_SESSION['user_id'])){
	
	header("Location: login.php");
	exit;
}else{		
	//se estiver tudo certo com a sessão adiciona config.  
	require_once("config.php");
}

if (defined('REQUIRED_PERMISSION')) {
    require_once "../controller/permissoesController.php";
    $pc = new permissoesController();
    if (!$pc->validaPermissao(REQUIRED_PERMISSION, $_SESSION['user_nivel'])) {
        header("Location: index.php");
        exit;
    }
}
?>