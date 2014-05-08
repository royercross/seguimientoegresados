<?php
@include("../../check.php");
$modificar=false;
require_once($rutaPHP."php/mysqlpdo.php");  
$mysql = new DBMannager();    
$mysql->connect();

if(isset($_POST['id'])){
	$id_usuario=$_SESSION['id_usuario'];
	$id=$_POST['id'];


	$query="SELECT * FROM alumnos WHERE id_alumno=? AND id_facultad=? AND status=1";		
	$mysql->execute($query,array($id,$_SESSION['id_facultad']));	
	if($mysql->count() < 1){die("ERROR");}
	$registro=$mysql->getRow();
	$modificar=true;
}
?>
  <!-- Modal -->
  <a class="close-reveal-modal">&#215;</a>  
  <h4 class="modal-title">Datos del Alumno</h4>
  <div class="alert-box info" >Por favor, comprueba tus datos y llena los campos faltantes.</div>        

  <form class="form-custom" method="post" action="" data-abide="ajax">      
    <div class="large-12 columns">
      <label>Generación</label>
      <div class="input-wrapper">
        <input value="<?=($modificar)?$alumno['generacion']:'';?>" id="txtGeneracion" name="txtGeneracion" type="text" placeholder="Generacion" required>            
        <small class="error">Campo requerido.</small>
      </div>
    </div>

    <!-- Text input-->
    <div class="large-12 columns">
      <label  for="txtNombre">Nombre</label>
      <div class="input-wrapper">
        <input value="<?=($modificar)?$alumno['nombre']:'';?>" id="txtNombre" name="txtNombre" type="text" placeholder="Nombre" class="input-xlarge"  required>            
      </div>
    </div>
                      
    <!-- Text input-->
    <div class="large-12 columns">
      <label  for="txtApellidoPaterno">Apellido Paterno</label>
      <div class="input-wrapper">
        <input value="<?=($modificar)?$alumno['apellido_paterno']:'';?>" id="txtApellidoPaterno" name="txtApellidoPaterno" type="text" placeholder="Apellido paterno" class="input-xlarge" required>
        
      </div>
    </div>
    
    <!-- Text input-->
    <div class="large-12 columns">
      <label  for="txtApellidoMaterno">Apellido Materno</label>
      <div class="input-wrapper">
        <input value="<?=($modificar)?$alumno['apellido_materno']:'';?>" id="txtApellidoMaterno" name="txtApellidoMaterno" type="text" placeholder="Apellido materno" class="input-xlarge" required>
        
      </div>
    </div>
    
    <!-- Text input-->
    <div class="large-12 columns">
      <label  for="cmbSexo">Sexo</label>
      <div class="input-wrapper">
        <select id="cmbSexo" name="cmbSexo" class="input-xlarge">
            <option <?=($modificar && $alumno['sexo']=='Masculino')?'selected':'';?> value="Matutino">Masculino</option>
            <option <?=($modificar && $alumno['sexo']=='Femenino')?'selected':'';?> value="Vespertino">Femenino</option>                    
        </select>
      </div>
    </div>       

    <!-- Text input-->
    <div class="large-12 columns">
      <label  for="txtEmail">Email</label>
      <div class="input-wrapper">
        <input value="<?=($modificar)?$alumno['email']:'';?>" id="txtEmail" name="txtEmail" type="email" placeholder="Emai" class="input-xlarge">
        
      </div>
    </div>
    
    <!-- Text input-->
    <div class="large-12 columns">
      <label  for="txtTelefonoCasa">Teléfono de Casa</label>
      <div class="input-wrapper">
        <input value="<?=($modificar)?$alumno['telefono_casa']:'';?>" id="txtTelefonoCasa" name="txtTelefonoCasa" type="text" placeholder="Teléfono de casa" class="input-xlarge">
        
      </div>
    </div>
    
    <!-- Text input-->
    <div class="large-12 columns">
      <label  for="txtTelefonoCelular">Teléfono Celular</label>
      <div class="input-wrapper">
        <input value="<?=($modificar)?$alumno['telefono_celular']:'';?>" id="txtTelefonoCelular" name="txtTelefonoCelular" type="text" placeholder="Teléfono celular" class="input-xlarge">
      </div>
    </div>
    
    <!-- Text input-->
    <div class="large-12 columns">
      <label  for="txtDomicilio">Domicilio</label>
      <div class="input-wrapper">
        <input value="<?=($modificar)?$alumno['domicilio']:'';?>" id="txtDomicilio" name="txtDomicilio" type="text" placeholder="Domicilio" class="input-xlarge">
      </div>
    </div>
    
    <!-- Text input-->
    <div class="large-12 columns">
      <label  for="txtColonia">Colonia</label>
      <div class="input-wrapper">
        <input value="<?=($modificar)?$alumno['colonia']:'';?>" id="txtColonia" name="txtColonia" type="text" placeholder="Colonia" class="input-xlarge">
      </div>
    </div>                        
    
    <!-- Text input-->
    <div class="large-12 columns">
      <label  for="txtCP">Código Postal</label>
      <div class="input-wrapper">
        <input value="<?=($modificar)?$alumno['cp']:'';?>" id="txtCP" name="txtCP" type="text" placeholder="Código Postal" class="input-xlarge">
      </div>
    </div>
    
    <!-- Text input-->
    <div class="large-12 columns">
      <label  for="cmbCarrera">Carrera</label>
      <div class="input-wrapper">
        <select id="cmbCarrera" name="cmbCarrera" class="input-xlarge">
            <?php
                $query="SELECT * FROM carreras WHERE id_facultad=? AND status=1";
                $mysql->execute($query,array($_SESSION['id_facultad']));      
                while($row=$mysql->getRow()){ 
                  $selected='';
                  if($modificar){
                    if($alumno['id_carrera']==$row['id_carrera']){
                      $selected='selected';
                    }
                  }
            ?>
                <option <?=$selected;?> value="<?=$row['id_carrera'];?>"><?=$row['nombre_carrera'];?></option>
            <?php } ?>
        </select>
      </div>
    </div>                                             
    
    <!-- Button -->
    <div class="large-12 columns">
      <label  for="btnGuardar"></label>
      <div class="input-wrapper">
        <button id="btnGuardar" name="btnGuardar" class="btn btn-primary"><?=($modificar)?'Actualizar alumno':'Guardar alumno';?></button>
        <input type="hidden" name="id" value="<?=($modificar)?$id:'0';?>" />
        <input type="hidden" name="accion" value="<?=($modificar)?'modifica':'agrega';?>" />      
      </div>
    </div>        
  </form>        
     