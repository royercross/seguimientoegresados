<?php /*require_once("../check.php");*/ ?>
<?php		
	session_start();

	if(!isset($_GET['q']))
		exit;
	
	require_once("../php/mysqlpdo.php");	
	$mysql = new DBMannager();		
	$mysql->connect();	

	$query="SELECT * FROM c_activos WHERE rkey=? AND status=1 ";		
	$mysql->execute($query,array($_GET['q']));

	if($mysql->count() < 1)
		exit;
		
	$row=$mysql->getRow();

	$_SESSION['rkey'] = $row['rkey'];
	$_SESSION['id_alumno'] = $row['id_alumno'];
?>
<?php 
	include("../config.inc.php");
	require_once("php/acciones_cuestionario_pise.php");
	require_once("../encabezado_interior.php"); 
	if(isset($_POST['final']) && $_POST['final']==1){ 
?>
		<div class="container-cuestionario">
			<div class="instrucciones alert-box success">Gracias por completar la encuesta, que tenga un día excelente.</div>
		</div>
<?php
		exit;
	}
?>
<?php



	if(!isset($_SESSION['seccion']))
	  $_SESSION['seccion']=0;

	if(isset($_GET['p']) && !isset($_POST['ignorep']))
	  $_SESSION['seccion']=$_GET['p'];

?>
<div class="container-cuestionario">
	<div class="instrucciones alert-box info">El siguiente cuestionario cuesta con 16 secciones, favor de contestar veridicamente</div>
	<?php
	
		$query="SELECT * FROM c_secciones WHERE id_cuestionario=1 and status=1";		
		$mysql->execute($query);

		$total_secciones=$mysql->count();
		$secciones=array();
		while($row=$mysql->getRow()){
			$secciones[$row['consecutivo']]['nombre_seccion'] = $row['nombre_seccion'];
			$secciones[$row['consecutivo']]['descripcion'] = $row['descripcion'];
		}
		
	?>	
	<ul class="button-group">
		<?php for($i=0; $i<$total_secciones; $i++){ ?>
  			<li><a href="pise.php?q=<?=$_SESSION['rkey'];?>&p=<?=$i;?>" class="button tiny <?=($_SESSION['seccion']==$i)?'success':'';?>"><?=$i;?></a></li>
  		<?php } ?>
	</ul>
	<h2><?=$secciones[$_SESSION['seccion']]['nombre_seccion'];?></h2>	
	<span class="instruccion-cuestionario"><?=$secciones[$_SESSION['seccion']]['descripcion'];?></span>
	<form method="post" action="">  
	<?php
		$query="SELECT a.*,b.respuesta_texto,b.respuesta_numero,b.respuesta_opcion_multiple,b.respuesta_opcion_multiple_otro,b.id_pregunta_tabla,b.respuesta_tabla_valor FROM c_preguntas a LEFT JOIN c_respuestas b ON (a.id_pregunta=b.id_pregunta AND b.id_alumno=? AND b.id_cuestionario=1) WHERE a.status=1 and a.id_cuestionario=1 and a.id_seccion=? GROUP BY a.id_pregunta";
		$mysql->execute($query,array($_SESSION['id_alumno'],$_SESSION['seccion']+1));			
		$hiddenValue=array();
		$contador=1;

		$preguntas = $mysql->getArray();

		foreach($preguntas as $pregunta){ 

			$campo="";
			/*** Tipo de Campo a Imprimir**/
			if($pregunta['id_tipo_pregunta'] == 1){
				$value="";
				if(strlen(trim($pregunta['respuesta_texto'])) > 0)
					$value=$pregunta['respuesta_texto'];

				$requerido="";
				if($pregunta['requerido'] == 1)
					$requerido="required";

				$campo="<input class='text' type='text' name='p_".$pregunta['id_pregunta']."' value='".$value."' $requerido />";
			}
			if($pregunta['id_tipo_pregunta'] == 2){
				$value="";
				if(strlen(trim($pregunta['respuesta_numero'])) > 0)
					$value=$pregunta['respuesta_numero'];

				$requerido="";
				if($pregunta['requerido'] == 1)
					$requerido="required";
				$campo="<input class='text' type='text' name='p_".$pregunta['id_pregunta']."' value='".$value."' $requerido />";
			}
			if($pregunta['id_tipo_pregunta'] == 3){				
				$query="SELECT * FROM c_opciones_multiples WHERE id_pregunta=? AND status=1";
				$mysql->execute($query,array($pregunta['id_pregunta']));
				$checked="checked";

				$value=NULL;
				if(strlen(trim($pregunta['respuesta_opcion_multiple'])) > 0)
					$value=$pregunta['respuesta_opcion_multiple'];

				$requerido="";
				if($pregunta['requerido'] == 1)
					$requerido="required";

				while($row=$mysql->getRow()){	
					$checked="";
					if($value == $row['valor']) $checked="checked";
					$campo.="<div class='radio-wrapper'><input class='radio' type='radio' name='p_".$pregunta['id_pregunta']."' value='".$row['valor']."' $requerido $checked /><span>".$row['opcion']."</span></div>";						
				}						
			}
			if($pregunta['id_tipo_pregunta'] == 4){				
				$query="SELECT * FROM c_opciones_multiples WHERE id_pregunta=? AND status=1";
				$mysql->execute($query,array($pregunta['id_pregunta']));
				$checked="checked";

				$value=NULL;
				if(strlen(trim($pregunta['respuesta_opcion_multiple'])) > 0)
					$value=$pregunta['respuesta_opcion_multiple'];
				
				$requerido="";
				if($pregunta['requerido'] == 1)
					$requerido="required";

				while($row=$mysql->getRow()){	
					$checked="";
					if($value == $row['valor']) $checked="checked";
					$campo.="<div class='radio-wrapper'><input class='radio' type='radio' name='p_".$pregunta['id_pregunta']."' value='".$row['valor']."' $requerido $checked /><span>".$row['opcion']."</span></div>";						
				}	
				$value_otro="";
				if(strlen(trim($pregunta['respuesta_opcion_multiple'])) > 0)
					$value_otro=$pregunta['respuesta_opcion_multiple_otro'];			
				$checked="";				
				if($value==100){
					$checked="checked";
				}
				$campo.="<div class='radio-wrapper'>";
				$campo.="<input class='radio' type='radio' name='p_".$pregunta['id_pregunta']."' value='100' $checked /><span>Otro (especifique):</span>";
				$campo.="<input class='text' type='text' name='p_".$pregunta['id_pregunta']."_otro' value='".$value_otro."' />";
				$campo.="</div>";					
			}		
			if($pregunta['id_tipo_pregunta'] == 5){		

				$query="SELECT * FROM c_opciones_tabla WHERE id_pregunta=? AND status=1";
				$mysql->execute($query,array($pregunta['id_pregunta']));				
				$opciones_tabla=$mysql->getArray();

				$query="SELECT * FROM c_preguntas_tabla WHERE id_pregunta=? AND status=1";
				$mysql->execute($query,array($pregunta['id_pregunta']));				
				$preguntas_tabla=$mysql->getArray();


				$query="SELECT * FROM c_respuestas WHERE id_pregunta=? AND id_cuestionario=1 and id_alumno=?";
				$mysql->execute($query,array($pregunta['id_pregunta'],$_SESSION['id_alumno']));	

				$respuestas=array();
				while($row=$mysql->getRow()){
					$respuestas[$row['id_pregunta_tabla']] = $row['respuesta_tabla_valor'];
				}				

				$campo.="<table class='table table-striped'>";
				$campo.="<thead><th>".$pregunta['encabezado_tabla']."</th>";
				foreach($opciones_tabla as $opcion_tabla){
					$campo.="<th class='text-center'>".$opcion_tabla['opcion']."</th>";
				}
				$campo.="</thead>";
				$campo.="<tbody>";
				foreach($preguntas_tabla as $pregunta_tabla){
					$campo.="<tr>";
					$campo.="<td>".$pregunta_tabla['pregunta']."</td>";
					$required="required";
					foreach($opciones_tabla as $opcion_tabla){
						$checked="";
						if(isset($respuestas[$pregunta_tabla['id_pregunta_tabla']]) && $respuestas[$pregunta_tabla['id_pregunta_tabla']] == $opcion_tabla['valor']){
							$checked="checked";
						}
						$campo.="<td class='text-center'><input class='radio' type='radio' name='p_".$pregunta['id_pregunta']."_".$pregunta_tabla['id_pregunta_tabla']."' value='".$opcion_tabla['valor']."' $required $checked /></td>";
						$required="";
					}	
					$campo.="</tr>";
				}
				$campo.="</tbody>";
				$campo.="</table>";
				/*
				$value=NULL;
				if(strlen(trim($pregunta['respuesta_opcion_multiple'])) > 0)
					$value=$pregunta['respuesta_opcion_multiple'];
				$required="required";
				while($row=$mysql->getRow()){	
					$checked="";
					if($value == $row['valor']) $checked="checked";
					$campo.="<div class='radio-wrapper'><input class='radio' type='radio' name='p_".$pregunta['id_pregunta']."' value='".$row['valor']."' $required $checked /><span>".$row['opcion']."</span></div>";						
				}	
				$value_otro="";
				if(strlen(trim($pregunta['respuesta_opcion_multiple'])) > 0)
					$value_otro=$pregunta['respuesta_opcion_multiple_otro'];			
				$checked="";				
				if($value==100){
					$checked="checked";
				}
				$campo.="<div class='radio-wrapper'>";
				$campo.="<input class='radio' type='radio' name='p_".$pregunta['id_pregunta']."' value='100' $checked /><span>Otro (especifique):</span>";
				$campo.="<input class='text' type='text' name='p_".$pregunta['id_pregunta']."_otro' value='".$value_otro."' />";
				$campo.="</div>";					
				*/
			}				
	?>
	<div class="pregunta-wrapper">
		<?php if($pregunta['id_tipo_pregunta'] == 5){ ?>
			<div class="pregunta-full"><?=$pregunta['pregunta'];?></div>
			<div class="respuesta-full"><?=$campo;?></div>
		<?php }else{ ?>
			<div class="pregunta"><?=$pregunta['pregunta'];?></div>
			<div class="respuesta"><?=$campo;?></div>
		<?php }?>
	</div>    	
    <?php 
    	array_push($hiddenValue,$pregunta['id_pregunta']); 
    	} 
    ?>
    <input type="hidden" name="ignorep" value="1" />
    <input type="hidden" name="hiddenIds" value="<?=implode(",",$hiddenValue);?>" /> 
    <?php if($_SESSION['seccion']==$total_secciones-1){ ?>
    	<input type="hidden" name="final" value="1" />
    	<div class="siguiente"><input type="submit" class="button" value="Finalizar" /> </div>
    <?php }else{ ?>

		<div class="siguiente"><input type="submit" class="button" value="Siguiente" /> </div>
	<?php } ?>
	</form>
</div>

<script src="<?=$ruta;?>js/jquery.dataTables.min.js"></script>
<script src="<?=$ruta;?>js/DT_bootstrap.js"></script>
<script src="<?=$ruta;?>js/TableTools.min.js"></script>    
<script src="<?=$ruta;?>js/ZeroClipboard.js"></script>    
<script>
	$(document).ready(function(){

	});

</script>
<?php include("../piepagina.php"); ?>