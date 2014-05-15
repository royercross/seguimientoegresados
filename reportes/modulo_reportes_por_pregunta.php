<?php require_once("../check.php"); ?> 
<?php require_once("../encabezado_interior.php"); ?>
<?php require_once("../menu.php"); ?>

<?php 
    $sidebar_selected=1; 
    require_once("../php/mysqlpdo.php");  
    $mysql = new DBMannager();    
    $mysql->connect();    
?>
  <section class="row fullwidth">
    <!--
    <section class="large-2 columns">
      <?php /*include("sidebar.php");**/ ?>
    </section>
    -->
    <section class="large-12 columns">
      <section class="panel filtros">
        <form action="" method="post">          
            <div class="input-wrapper large-2 columns">
              <select id="cmbPregunta" name="cmbPregunta" class="">
                  <option value="all">Todas las preguntas</option>
                  <?php
                      $query="SELECT * FROM c_preguntas WHERE id_cuestionario=1 AND status=1 AND id_tipo_pregunta<>1";
                      $mysql->execute($query);      
                      while($row=$mysql->getRow()){ 
                        $selected='';
                  ?>
                      <option <?=$selected;?> value="<?=$row['id_pregunta'];?>"><?=$row['pregunta'];?></option>
                  <?php } ?>               
              </select>
            </div>     
            <div class="input-wrapper large-2 columns">         
              <select id="cmbCarrera" name="cmbCarrera" class="">
                  <option value="all">Todas las carreras</option>
                  <?php
                      $query="SELECT * FROM carreras WHERE id_facultad=? AND status=1";
                      $mysql->execute($query,array($_SESSION['id_facultad']));      
                      while($row=$mysql->getRow()){ 
                        $selected='';/*
                        if($modificar){
                          if($alumno['id_carrera']==$row['id_carrera']){
                            $selected='selected';
                          }
                        }*/
                  ?>
                      <option <?=$selected;?> value="<?=$row['id_carrera'];?>"><?=$row['nombre_carrera'];?></option>
                  <?php } ?>
              </select>
              <!--<small class="error">Campo requerido.</small>-->
            </div>
            <div class="input-wrapper large-2 columns">
              <select id="cmbGeneracion" name="cmbGeneracion" class="">
                  <option value="all">Todas las generaciones</option>
                  <?php
                      $query="SELECT DISTINCT generacion FROM alumnos WHERE id_facultad=? AND status=1";
                      $mysql->execute($query,array($_SESSION['id_facultad']));      
                      while($row=$mysql->getRow()){ 
                  ?>
                      <option value="<?=$row['generacion'];?>"><?=$row['generacion'];?></option>
                  <?php } ?>
              </select>
              <!--<small class="error">Campo requerido.</small>-->
            </div>               
            <div class="input-wrapper large-2 columns end">
              <input id="btnBuscar" type="button" class="button" value="buscar">
              <!--<small class="error">Campo requerido.</small>-->
            </div>                   
        </form>
      </section>
      <section id="grafica" class="grafica">
        
      </section>

      <?php

        $query="SELECT a.*,b.rkey FROM alumnos a LEFT JOIN c_activos b ON a.id_alumno=b.id_alumno WHERE a.id_facultad=? AND a.status=1 ORDER BY a.id_alumno DESC";   
        $mysql->execute($query,array($_SESSION['id_facultad']));             
      ?>
      </section>
    </section>
<?php $loadScripts=false; ?>
<script src="<?=$ruta;?>lib/jquery.js"></script>
<script src="<?=$ruta;?>lib/foundation.min.js"></script>
<script src="<?=$ruta;?>lib/foundation.abide.js"></script>
<script src="<?=$ruta;?>lib/foundation.alert.js"></script>
<script src="<?=$ruta;?>lib/highcharts.js"></script>
<script src="<?=$ruta;?>lib/modules/data.js"></script>
<script src="<?=$ruta;?>lib/modules/exporting.js"></script>
    
<script>
	$(document).ready(function(){
  
    $('#btnBuscar').click(function(){  
      var carrera=$("#cmbCarrera").val();
      var generacion=$("#cmbGeneracion").val();
      var pregunta=$("#cmbPregunta").val();
      $.post("php/graficas.php",{ carrera: carrera, generacion: generacion, pregunta: pregunta}, function(html){   
          $("#grafica").html(html);          
      });
    });
		
	});


</script>
<?php include("../piepagina.php"); ?>