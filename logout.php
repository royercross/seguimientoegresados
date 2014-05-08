<?php
	include("config.inc.php");
	session_start();
	session_destroy();
	header("Location: ".$ruta."");
?>