<?php

if(isset($_POST['hiddenIds']) && isset($_SESSION['rkey']) && isset($_SESSION['id_alumno']) && isset($_SESSION['seccion'])){

	if(isset($_GET['p']))
	  $_GET['p']++;

	$ids=$_POST['hiddenIds'];
	require_once("../php/mysqlpdo.php");	
	$mysql = new DBMannager();		
	$mysql->connect();	
	

	/*** Separa los  Id y Obtiene la cadena ?,?,?,? ***/
	$ids = explode(",",$ids);
	$placeholders = rtrim(str_repeat('?, ', count($ids)),', ');
	
	/*** Obtiene las preguntas y su id_respuesta si existe **/	
	$query="SELECT a.*,b.id_respuesta FROM c_preguntas a LEFT JOIN c_respuestas b ON (a.id_pregunta=b.id_pregunta AND b.id_alumno=?) WHERE a.id_pregunta IN ($placeholders) GROUP BY a.id_pregunta";
	$mysql->execute($query,array_merge(array($_SESSION['id_alumno']),$ids));
	
	
	/*** Guarda el tipo y respuesta de cada pregunta**/
	$preguntas_tipos=array();

	while($row=$mysql->getRow()){
		$preguntas_tipos[$row['id_pregunta']]['tipo'] = $row['id_tipo_pregunta'];
		if(!is_null($row['id_respuesta']))
			$preguntas_tipos[$row['id_pregunta']]['respuesta'] = $row['id_respuesta'];
		else
			$preguntas_tipos[$row['id_pregunta']]['respuesta'] = 0;
	}
	
	

	$preguntas=array();

	foreach ($ids as $id) {
		if($preguntas_tipos[$id]['tipo'] == 5){
			array_push($preguntas,array("id"=>$id, "valor"=>0));
		}else{
			array_push($preguntas,array("id"=>$id, "valor"=>$_POST['p_'.$id]));
		}
		
	}

	foreach ($preguntas as $pregunta) {
		if($preguntas_tipos[$pregunta["id"]]['tipo'] == 1){
			if($preguntas_tipos[$pregunta['id']]['respuesta'] > 0 ){
				$query="UPDATE c_respuestas SET respuesta_texto=? WHERE id_respuesta=?";	
				$mysql->execute($query,array($pregunta["valor"],$preguntas_tipos[$pregunta['id']]['respuesta']));
			}else{
				$query="INSERT INTO c_respuestas(id_pregunta,id_cuestionario,id_alumno,id_tipo_pregunta,respuesta_texto) VALUES (?,?,?,?,?)";	
				$mysql->execute($query,array($pregunta["id"],1,$_SESSION['id_alumno'],1,$pregunta["valor"]));	
			}			
		}
		if($preguntas_tipos[$pregunta["id"]]['tipo'] == 2){
			if($preguntas_tipos[$pregunta['id']]['respuesta'] > 0 ){
				$query="UPDATE c_respuestas SET respuesta_numero=? WHERE id_respuesta=?";	
				$mysql->execute($query,array($pregunta["valor"],$preguntas_tipos[$pregunta['id']]['respuesta']));
			}else{
				$query="INSERT INTO c_respuestas(id_pregunta,id_cuestionario,id_alumno,id_tipo_pregunta,respuesta_numero) VALUES (?,?,?,?,?)";	
				$mysql->execute($query,array($pregunta["id"],1,$_SESSION['id_alumno'],2,$pregunta["valor"]));	
			}			
		}	
		if($preguntas_tipos[$pregunta["id"]]['tipo'] == 3){
			if($preguntas_tipos[$pregunta['id']]['respuesta'] > 0 ){
				$query="UPDATE c_respuestas SET respuesta_opcion_multiple=? WHERE id_respuesta=?";	
				$mysql->execute($query,array($pregunta["valor"],$preguntas_tipos[$pregunta['id']]['respuesta']));
			}else{
				$query="INSERT INTO c_respuestas(id_pregunta,id_cuestionario,id_alumno,id_tipo_pregunta,respuesta_opcion_multiple) VALUES (?,?,?,?,?)";	
				$mysql->execute($query,array($pregunta["id"],1,$_SESSION['id_alumno'],3,$pregunta["valor"]));	
			}			
		}			
		if($preguntas_tipos[$pregunta["id"]]['tipo'] == 4){

			$otro=false;
			$valor_otro="";
			if($pregunta["valor"] == 100){
				$otro=true;
				if(!isset($_POST['p_'.$pregunta["id"].'_otro'])) exit;
				$valor_otro = $_POST['p_'.$pregunta["id"].'_otro'];
			}

			if($preguntas_tipos[$pregunta['id']]['respuesta'] > 0 ){
				$query="UPDATE c_respuestas SET respuesta_opcion_multiple=?, respuesta_opcion_multiple_otro=? WHERE id_respuesta=?";	
				$mysql->execute($query,array($pregunta["valor"],$valor_otro,$preguntas_tipos[$pregunta['id']]['respuesta']));					
			}else{
				$query="INSERT INTO c_respuestas(id_pregunta,id_cuestionario,id_alumno,id_tipo_pregunta,respuesta_opcion_multiple,respuesta_opcion_multiple_otro) VALUES (?,?,?,?,?,?)";	
				$mysql->execute($query,array($pregunta["id"],1,$_SESSION['id_alumno'],4,$pregunta["valor"],$valor_otro));	
			}			
		}	
		if($preguntas_tipos[$pregunta["id"]]['tipo'] == 5){
			$keyMatch = "p_".$pregunta["id"];
			$arrPreguntasTabla=array();
			foreach($_POST as $key => $value){
				if(substr($key,0,strlen($keyMatch)) == $keyMatch){
					$temp = explode("_",$key);
					array_push($arrPreguntasTabla, array("id_pregunta_tabla" => $temp[2], "valor" => $value));
				}
			}

			$query="SELECT * FROM c_respuestas WHERE id_pregunta=? AND id_cuestionario=1 and id_alumno=?";
			$mysql->execute($query,array($pregunta['id'],$_SESSION['id_alumno']));	

			$respuestas=array();
			while($row=$mysql->getRow()){
				$respuestas[$row['id_pregunta_tabla']]['id_respuesta'] = $row['id_respuesta'];				
			}	

			//print_r($respuestas);

			foreach($arrPreguntasTabla as $pregunta_tabla){

				if(isset($respuestas[$pregunta_tabla['id_pregunta_tabla']]['id_respuesta'])){
					$query="UPDATE c_respuestas SET respuesta_tabla_valor=? WHERE id_respuesta=?";
					$mysql->execute($query,array($pregunta_tabla['valor'], $respuestas[$pregunta_tabla['id_pregunta_tabla']]['id_respuesta']));
				}else{
					$query="INSERT INTO c_respuestas(id_pregunta,id_cuestionario,id_alumno,id_tipo_pregunta,id_pregunta_tabla,respuesta_tabla_valor) VALUES (?,?,?,?,?,?)";	
					$mysql->execute($query,array($pregunta["id"],1,$_SESSION['id_alumno'],5,$pregunta_tabla['id_pregunta_tabla'],$pregunta_tabla['valor']));					
				}
			}

			//print_r($arrPreguntasTabla);
			/*
			$otro=false;
			$valor_otro="";
			if($pregunta["valor"] == 100){
				$otro=true;
				if(!isset($_POST['p_'.$pregunta["id"].'_otro'])) exit;
				$valor_otro = $_POST['p_'.$pregunta["id"].'_otro'];
			}

			if($preguntas_tipos[$pregunta['id']]['respuesta'] > 0 ){
				$query="UPDATE c_respuestas SET respuesta_opcion_multiple=?, respuesta_opcion_multiple_otro=? WHERE id_respuesta=?";	
				$mysql->execute($query,array($pregunta["valor"],$valor_otro,$preguntas_tipos[$pregunta['id']]['respuesta']));					
			}else{
				$query="INSERT INTO c_respuestas(id_pregunta,id_cuestionario,id_alumno,id_tipo_pregunta,respuesta_opcion_multiple,respuesta_opcion_multiple_otro) VALUES (?,?,?,?,?,?)";	
				$mysql->execute($query,array($pregunta["id"],1,$_SESSION['id_alumno'],3,$pregunta["valor"],$valor_otro));	
			}	
			*/		
		}					
	}
	
	$_SESSION['seccion']++;
}

?>