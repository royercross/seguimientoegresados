<?php
@include("../../check.php");
if(isset($_POST['accion'])){
	$accion=$_POST['accion'];	
	$id_usuario=$_SESSION['id_usuario'];
	$id_facultad=$_SESSION['id_facultad'];

	$generacion=$nombre=$_POST['txtGeneracion'];
	$nombre=$_POST['txtNombre'];
	$apellido_paterno=$_POST['txtApellidoPaterno'];
	$apellido_materno=$_POST['txtApellidoMaterno'];
	$sexo=$_POST['cmbSexo'];
	$email=$_POST['txtEmail'];
	$telefono_casa=$_POST['txtTelefonoCasa'];
	$telefono_celular=$_POST['txtTelefonoCelular'];
	$domicilio=$_POST['txtDomicilio'];
	$colonia=$_POST['txtColonia'];

	$id_carrera=$_POST['cmbCarrera'];

	require_once($rutaPHP."php/mysqlpdo.php");	
	$mysql = new DBMannager();		
	$mysql->connect();	
	
	
	if($accion=='agrega'){
		if(isset($_POST['txtNombre']) && isset($_POST['txtApellidoPaterno']) && isset($_POST['txtApellidoMaterno'])){
			$query="INSERT INTO alumnos(generacion,nombre,apellido_paterno,apellido_materno,sexo,email,telefono_casa,telefono_celular,domicilio,colonia,id_carrera,id_facultad) VALUES(?,?,?,?,?,?,?,?,?,?,?,?)";
			print_r (array($generacion,$nombre,$apellido_paterno,$apellido_materno,$sexo,$email,$telefono_casa,$telefono_celular,$domicilio,$colonia,$id_carrera,$id_facultad));
			$mysql->execute($query,array($generacion,$nombre,$apellido_paterno,$apellido_materno,$sexo,$email,$telefono_casa,$telefono_celular,$domicilio,$colonia,$id_carrera,$id_facultad));				
			$last_id=$mysql->getlastInsertedId();
			$query="INSERT INTO c_activos (id_alumno,rkey) VALUES (?,?)";
			$mysql->execute($query,array($last_id,md5($last_id.date())));
			$_SESSION['mensaje']="El alumno ha sido agregado correctamente.";	
		}
		//header('Location: modulo_alumnos.php');	
		//exit;
	}else
	if($accion=='modifica'){
		/*
		$id=$_POST['id'];
		$query="UPDATE cc_actividades_extracurriculares SET nombre_actividad=?, creditos=? WHERE id_actividad_extracurricular=?";
		$mysql->execute($query,array($nombre_actividad,$creditos,$id));	
		$_SESSION['mensaje']="La actividad extracurricular ha sido actualizada correctamente.";	
		header('Location: modulo_alumnos.php');	
		*/
	}else
	if($accion=='eliminar'){		
		$id=$_POST['id_alumno'];
		$query="UPDATE alumnos SET status=2,fecha_modificacion=NOW(),id_usuario_modificacion=? WHERE id_alumno=?";
		//echo $query;
		$mysql->execute($query,array($id_usuario,$id));
		$_SESSION['mensaje']="El alumno se ha borrado satisfactoriamente.";	
		//header('Location: modulo_historial_creditos.php');						
	}

	//exit;
}

?>