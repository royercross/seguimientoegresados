<?php
	include("config.inc.php");
	define('MAX_IDLE_TIME',60*60);	
	session_start();
	$sesion_valida=true;
	if(!isset($_SESSION["REMOTE_IP"]) || $_SESSION["REMOTE_IP"]!=$_SERVER['REMOTE_ADDR'] || $_SESSION["TOKEN"]!="0a287b25c3570b784675e3aa3ef07892")
		$sesion_valida=false;

	
	if(!isset($_SESSION['timeout_idle']))
		$_SESSION['timeout_idle'] = time() + MAX_IDLE_TIME;
	else 
		if ($_SESSION['timeout_idle'] > time()) 
			$_SESSION['timeout_idle'] = time() + MAX_IDLE_TIME;	
		else
			$sesion_valida=false;		
		
	if(!$sesion_valida)
		header("Location: ".$ruta."logout.php");
?>