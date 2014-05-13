<?php require_once("../check.php"); ?>
<?php require_once("php/acciones_modulo_alumnos.php"); ?>
<?php require_once("../encabezado_interior.php"); ?>
<?php require_once("../menu.php"); ?>

  <?php $sidebar_selected=1; ?>
  <section class="row fullwidth">
    <!--
    <section class="large-2 columns">
      <?php /*include("sidebar.php");**/ ?>
    </section>
    -->
    <section class="large-12 columns">
      <?php
        require_once("../php/mysqlpdo.php");  
        $mysql = new DBMannager();    
        $mysql->connect();    
        $query="SELECT a.*,b.rkey FROM alumnos a LEFT JOIN c_activos b ON a.id_alumno=b.id_alumno WHERE a.id_facultad=? AND a.status=1 ORDER BY a.id_alumno DESC";   
        $mysql->execute($query,array($_SESSION['id_facultad']));      
        if($mysql->count() < 1){    
      ?>      
        <div class="contenido">    

          <a href="#" class="button"  id="btnAgregar">Agregar Alumno</a>
          <?php if(isset($_SESSION['mensaje'])){?>
          <div class="alert-box success alert-hide"><?=$_SESSION['mensaje'];?></div>       
          <?php unset($_SESSION['mensaje']);} ?>
          <div class="alert alert-error" >No se encontraron alumnos.</div>                    
        </div>
      <?php
      }else{
    ?>
      <div class="contenido">
        <?php if(isset($_SESSION['mensaje'])){?>
        <div class="alert-box success alert-hide"><?=$_SESSION['mensaje'];?></div>       
        <?php unset($_SESSION['mensaje']);} ?>      
        <a href="#" class="button"  id="btnAgregar">Agregar Alumno</a>
      <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="tabla">    
            <thead>
                <tr>
                      <th>Generación</th>
                      <th>Nombre</th>
                      <th>Apellido Paterno</th>
                      <th>Apellido Materno</th>
                      <th>Telefono</th>
                      <th>Celular</th>
                      <th>E-mail</th>
                      <th>cuestionario 1</th>
                      <th>Opciones</th>
                  </tr>
              </thead>
              <tbody>
                <?php while($alumno=$mysql->getRow()){ ?>
                <tr>
                      <td><?=$alumno['generacion'];?></td>
                      <td><?=$alumno['nombre'];?></td>
                      <td><?=$alumno['apellido_paterno'];?></td>
                      <td><?=$alumno['apellido_materno'];?></td>                    
                      <td><?=$alumno['telefono_casa'];?></td>                    
                      <td><?=$alumno['telefono_celular'];?></td>
                      <td><?=$alumno['email'];?></td>
                      <td><a href="http://localhost/seguimientoegresados/quiz/pise.php?q=<?=$alumno['rkey'];?>" target="_blank">http://localhost/seguimientoegresados/cuestionarios/pise.php?q=<?=$alumno['rkey'];?></a></td>   
                      <td class="tabla-acciones">
                       <!--<a href="#" class="icon-edit" onclick="ver(<?=$alumno['id_alumno'];?>);return false;" style="margin-right:10px;"></a>-->
                        <a href="#" class="fi-x" onclick="eliminar(<?=$alumno['id_alumno'];?>);return false;" ></a>
                        <form style="display:none;" name="form<?=$alumno['id_alumno'];?>" id="form<?=$alumno['id_alumno'];?>" method="post">
                          <input type="hidden" name="id_alumno" value="<?=$alumno['id_alumno'];?>" />
                          <input type="hidden" name="accion" value="eliminar" />                            
                        </form>
                    </td>
                  </tr> 
                  <?php } ?>
              </tbody>
          </table>
      </div>    
      <?php } ?>            
    </section>
  </section>

  <div id="modal-wrapper" class="reveal-modal" data-reveal></div>
</div>
<?php $loadScripts=false; ?>
<script src="<?=$ruta;?>lib/jquery.js"></script>
<script src="<?=$ruta;?>lib/foundation.min.js"></script>
<script src="<?=$ruta;?>lib/foundation.abide.js"></script>
<script src="<?=$ruta;?>lib/foundation.alert.js"></script>
<script src="<?=$ruta;?>lib/jquery.dataTables.min.js"></script>
<script src="<?=$ruta;?>lib/dataTables.foundation.js"></script>

    
<script>
	$(document).ready(function(){
		/* Table initialisation */
    /*
		$('#example').dataTable( {
			"sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
			"sPaginationType": "bootstrap",
			"oLanguage": {
				"sUrl": "../js/languages/es_MX.txt"
			}
		} );
    */

    $("#tabla").dataTable({
      "oLanguage": {
        "sUrl": "../lib/languages/es_MX.txt"
      },
      iDisplayLength: 50,
      aaSorting:[]
    });
  
    $('#btnAgregar').click(function(){  
      $.get("php/form_alumnos.php", function(html){   
          $("#modal-wrapper").html(html);
          $('#modal-wrapper').foundation('reveal', 'open');
          $('#modal-wrapper').foundation({bindings:'events'});
      });
    });

		$('#btnReset').click(function(){
				bootbox.confirm("¿Seguro que desea actualizar los datos solicitados por el alumno?",function(resultado){
					if(resultado){
						$('#form-resetpass').submit();
					}
				});
		});				
	});

  function eliminar(id){    
    if(confirm("¿Seguro que deseas eliminar al alumno seleccionado?")){    
        $('#form'+id).submit();
    }   
  }
</script>
<?php include("../piepagina.php"); ?>