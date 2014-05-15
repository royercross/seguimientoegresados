<?php require_once("../../check.php"); ?> 


<?php  

    if( !isset($_POST['carrera']) || !isset($_POST['generacion']))
        die();


    if(isset($_POST['pregunta'])){
        $pregunta=$_POST['pregunta'];
    }else
    if(isset($_POST['grupo'])){
        $grupo=$_POST['grupo'];
    }

    require_once("../../php/mysqlpdo.php");  
    $mysql = new DBMannager();    
    $mysql->connect();   

    $carrera=$_POST['carrera'];
    $generacion=$_POST['generacion'];    
        
    $panel="";
    $preguntas = array();
    $filtros="";
    if($carrera!="all"){
        $filtros.=sprintf(" AND b.id_carrera='%s' ",mysql_real_escape_string($carrera));
    }
    if($generacion!="all"){
        $filtros.=sprintf(" AND b.generacion='%s' ",mysql_real_escape_string($generacion));
    }
    $individuales=false;
    if(isset($grupo) && $grupo!="all"){     
        switch($grupo){
            case 2: $preguntas_id = array(7,8,9); break;
            case 3: $preguntas_id = array(13,17,18,19,22);break;
            case 4: $preguntas_id = array(25,26,27,28,35,37,43,40,42,39);break;
        }
        $individuales=true;
    }else
    if(isset($pregunta) && $pregunta!="all"){
        $preguntas_id = array($pregunta);
        $individuales=true;
    }
    if($individuales){
        $query="SELECT * FROM c_preguntas WHERE id_pregunta IN (".implode(",",$preguntas_id).") ";        
        //$mysql->execute($query,array($_SESSION['id_facultad']));   

        $mysql->execute($query);   
        $preguntas = $mysql->getArray();
        for($i=0; $i < count($preguntas); $i++){           

            if($preguntas[$i]['id_tipo_pregunta'] == 3 || $preguntas[$i]['id_tipo_pregunta'] == 4) {
                $query="SELECT * FROM c_opciones_multiples WHERE id_pregunta = ?";
                $mysql->execute($query,array($preguntas[$i]['id_pregunta']));   

                $respuestas = $mysql->getArray();
                $temporal = array();
                foreach($respuestas as $respuesta){
                    $temporal[$respuesta['valor']] = $respuesta['opcion'];
                }
                $preguntas[$i]['opciones'] = $temporal;
            }
            
        }
        //print_r($preguntas);
        //$first=true;
?>
      <dl class="tabs" data-tab>
        <?php
            $cont=1;
            $first=true;
            foreach($preguntas as $pregunta){
        ?>
                <dd class="<?php if($first){$first=false;echo 'active';}?>"><a href="#panel-<?=$cont;?>"><?=$pregunta['pregunta'];?></a></dd>
        <?php
                $cont++;
            }
        ?>
      </dl>
      <div class="tabs-content">
        <?php
            $first=true;
            $cont=1;
            foreach($preguntas as $pregunta_key=>$pregunta){

                $query="SELECT * FROM c_respuestas a INNER JOIN alumnos b ON a.id_alumno=b.id_alumno WHERE b.id_facultad=? AND a.id_pregunta IN (".implode(",",$preguntas_id).")".$filtros;
                $mysql->execute($query,array($_SESSION['id_facultad']));                   
                $respuestas = $mysql->getArray();
                $arrRespuestas=array();


                /** ANALIZA LAS RESPUESTAS SEGUN EL TIPO DE PREGUNTA **/
                foreach($respuestas as $respuesta){
                    if(!isset($arrRespuestas[$respuesta['id_pregunta']]['total'])){
                        $arrRespuestas[$respuesta['id_pregunta']]['total']=0;
                    }
                    if($respuesta['id_tipo_pregunta']==2){
                        if(!isset($arrRespuestas[$respuesta['id_pregunta']][$respuesta['respuesta_numero']]))
                        {
                            $arrRespuestas[$respuesta['id_pregunta']][$respuesta['respuesta_numero']]=1;
                            $arrRespuestas[$respuesta['id_pregunta']]['total']++;
                        }else{
                            $arrRespuestas[$respuesta['id_pregunta']][$respuesta['respuesta_numero']]++;
                            $arrRespuestas[$respuesta['id_pregunta']]['total']++;
                        }
                    }else
                    if($respuesta['id_tipo_pregunta']==3 || $respuesta['id_tipo_pregunta']==4){ 
                        
                        if(!isset($arrRespuestas[$respuesta['id_pregunta']][$respuesta['respuesta_opcion_multiple']]))
                        {
                            $arrRespuestas[$respuesta['id_pregunta']][$respuesta['respuesta_opcion_multiple']]=1;                            
                            $arrRespuestas[$respuesta['id_pregunta']]['total']++;
                        }else{
                            $arrRespuestas[$respuesta['id_pregunta']][$respuesta['respuesta_opcion_multiple']]++;
                            $arrRespuestas[$respuesta['id_pregunta']]['total']++;                            
                        }                        
                    }else
                    if($respuesta['id_tipo_pregunta']==5){                         
                        if(!isset($arrRespuestas[$respuesta['id_pregunta']][$respuesta['id_pregunta_tabla']]['total'])){
                            $arrRespuestas[$respuesta['id_pregunta']][$respuesta['id_pregunta_tabla']]['total']=0;                            
                        }
                        if(!isset($arrRespuestas[$respuesta['id_pregunta']][$respuesta['id_pregunta_tabla']][$respuesta['respuesta_tabla_valor']]))
                        {                            
                            $arrRespuestas[$respuesta['id_pregunta']][$respuesta['id_pregunta_tabla']][$respuesta['respuesta_tabla_valor']]=1;
                            $arrRespuestas[$respuesta['id_pregunta']][$respuesta['id_pregunta_tabla']]['total']++;
                            //$arrRespuestas[$respuesta['id_pregunta']][$respuesta['id_pregunta_tabla']][$respuesta['respuesta_tabla_valor']]['total']++;
                        }else{
                            $arrRespuestas[$respuesta['id_pregunta']][$respuesta['id_pregunta_tabla']][$respuesta['respuesta_tabla_valor']]++;
                            $arrRespuestas[$respuesta['id_pregunta']][$respuesta['id_pregunta_tabla']]['total']++;
                            //$arrRespuestas[$respuesta['id_pregunta']][$respuesta['id_pregunta_tabla']][$respuesta['respuesta_tabla_valor']]['total']++;
                        }                                                
                    }
                    
                }

                //print_r($pregunta['opciones']);
                //print_r($arrRespuestas);

        ?>                         
            <div class="content <?php if($first){$first=false;echo 'active';}?>" id="panel-<?=$cont;?>">
                <div class="rows">
                    <section class="small-12 large-3 columns no-padding">
                        <?php if($pregunta['id_tipo_pregunta']==2 || $pregunta['id_tipo_pregunta']==3 || $pregunta['id_tipo_pregunta']==4){ ?>
                        <table id="tabla-datos-<?=$cont;?>" class="tabla-datos">
                            <thead>
                                <th></th>
                                <th>Cantidad</th>
                                <th>%</th>
                            </thead>
                            <tbody> 
                                <?php 
                                    if($pregunta['id_tipo_pregunta'] == 2){
                                        ksort($arrRespuestas[$pregunta['id_pregunta']]);
                                 ?>
                                    <?php foreach($arrRespuestas[$pregunta['id_pregunta']] as $key=>$value){ ?>  
                                        <?php if($key!="total") { ?>
                                            <tr>
                                                <td>[<?=$key;?>]</td>
                                                <td><?=(isset($arrRespuestas[$pregunta['id_pregunta']][$key]))?$arrRespuestas[$pregunta['id_pregunta']][$key]:'0';?></td>
                                                <td><?=(isset($arrRespuestas[$pregunta['id_pregunta']][$key]))?round($arrRespuestas[$pregunta['id_pregunta']][$key]*100/$arrRespuestas[$pregunta['id_pregunta']]['total'],2):'0';?></td>
                                            </tr>
                                        <?php } ?>
                                    <?php } ?>
                                    <?php $preguntas[$pregunta_key]['total_row']="<tr><th>Total</th><th>".$arrRespuestas[$pregunta['id_pregunta']]['total']."</th><th>100%</th></tr>"; ?>
                                <?php } ?>                            
                                <?php if($pregunta['id_tipo_pregunta'] == 3 || $pregunta['id_tipo_pregunta'] == 4){ ?>
                                    <?php foreach($pregunta['opciones'] as $key=>$value){ ?>
                                        <tr>
                                            <td><?=$value;?></td>
                                            <td><?=(isset($arrRespuestas[$pregunta['id_pregunta']][$key]))?$arrRespuestas[$pregunta['id_pregunta']][$key]:'0';?></td>
                                            <td><?=(isset($arrRespuestas[$pregunta['id_pregunta']][$key]))?round($arrRespuestas[$pregunta['id_pregunta']][$key]*100/$arrRespuestas[$pregunta['id_pregunta']]['total'],2):'0';?></td>
                                        </tr>
                                    <?php } ?>
                                    <?php $preguntas[$pregunta_key]['total_row']="<tr><th>Total</th><th>".$arrRespuestas[$pregunta['id_pregunta']]['total']."</th><th>100%</th></tr>"; ?>
                                <?php } ?>                                                          
                            </tbody>
                        </table>
                        <?php 
                            }else
                            if($pregunta['id_tipo_pregunta']==5){
                                    //var_dump($arrRespuestas);
                        ?>
                         <table id="tabla-datos-<?=$cont;?>" class="tabla-datos">
                            <thead>
                                <th>Categorías</th>
                                <?php                                
                                $query="SELECT * FROM c_opciones_tabla WHERE id_pregunta = ?";        
                                $mysql->execute($query,array($pregunta['id_pregunta']));

                                $opciones_tabla = $mysql->getArray();



                                foreach($opciones_tabla as $opcion_tabla){

                                ?>
                                    <th><?=$opcion_tabla['opcion'];?></th>
                                <?php } ?>  
                                <th>Total</th>                              
                            </thead>
                            <tbody>
                                <?php
                                     $query="SELECT * FROM c_preguntas_tabla WHERE id_pregunta = ?";
                                    $mysql->execute($query,array($pregunta['id_pregunta']));

                                    $opciones = $mysql->getArray();
                                    foreach($opciones as $opcion){
                                ?>
                                        <tr>
                                            <td><?=$opcion['pregunta'];?></td>
                                            <?php 
                                                foreach($opciones_tabla as $opcion_tabla){                                                     
                                                    if(!isset($arrRespuestas[$pregunta['id_pregunta']][$opcion['id_pregunta_tabla']][$opcion_tabla['valor']]))
                                                        $val=0;
                                                    else
                                                        $val=$arrRespuestas[$pregunta['id_pregunta']][$opcion['id_pregunta_tabla']][$opcion_tabla['valor']];
                                            ?>
                                                <td><?=$val;?></td>
                                            <?php } ?>
                                            <td><?=$arrRespuestas[$respuesta['id_pregunta']][$opcion['id_pregunta_tabla']]['total'];?></td>
                                        </tr>
                                <?php
                                    }
                                ?>
                            </tbody>
                        </table>
                        <?php
                            }
                         ?>
                    </section>
                    <section class="small-12 large-8 columns no-padding">
                        <div id="panel-grafica-<?=$cont;?>"></div>            
                    </section>
                </div>      
            </div>
        <?php
            $cont++;
            }
        ?>      
      </div>


    <script>
        $(document).ready(function(){
            $('#grafica').foundation();
    <?php
        $cont=1;

        foreach($preguntas as $pregunta){
    ?>
            $('#panel-grafica-<?=$cont;?>').highcharts({
                data: {
                    table: document.getElementById('tabla-datos-<?=$cont;?>')
                },
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false
                },
                title: {
                    text: '<?=$pregunta['pregunta'];?>'
                },          
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b>: {point.y} ({point.percentage:.1f} %)',
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                            }
                        }
                    }
                },
                series: [{
                    type: 'pie',
                    name: 'Carreras'
                }]                
            });     
            $("#tabla-datos-<?=$cont;?>").append("<?=$pregunta['total_row'];?>");
    <?php
        $cont++;
        }
    ?>
        });
    </script>      
<?php
    }else{
        $panel="panel-grafica";
?>
<div class="rows">
<section class="small-12 large-3 columns no-padding">
<table id="tabla-datos" class="tabla-datos">
    <thead>
        <th>Carreras</th>
        <th>Alumnos</th>
        <th>%</th>
    </thead>
    <tbody>        
<?php
    // Construye los datos
    $query="SELECT a.*,COUNT(b.id_alumno) as total_alumnos FROM carreras a 
LEFT JOIN alumnos b ON a.id_carrera=b.id_carrera 
WHERE a.id_facultad=? AND a.status=1 ".$filtros." GROUP BY a.id_carrera";
    $mysql->execute($query,array($_SESSION['id_facultad']));   
    $data=array();   
    $total=0;
    $arrCarreras = $mysql->getArray();
    foreach($arrCarreras as $carrera){
        $total+=$carrera['total_alumnos'];
    }
    foreach($arrCarreras as $carrera){            
?>
        <tr>
            <th><?=$carrera['nombre_carrera'];?></th>
            <td><?=$carrera['total_alumnos'];?></td>
            <td><?=round($carrera['total_alumnos']*100/$total,2);?></td>
        </tr>
<?php
    }
    $data = implode(",",$data);
    $total_row="<tr><th>Total</th><th>$total</th><th>100%</th></tr>";
?>
    </tbody>
</table>
</section>
<section class="small-12 large-8 columns no-padding">
<div id="panel-grafica"></div>            
</section>
</div>

<script>
    $(document).ready(function(){

        $('#grafica').foundation();
        $('#<?php echo $panel; ?>').highcharts({
            data: {
                table: document.getElementById('tabla-datos')
            },
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Alumnos por Carrera'
            },                    
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        format: '<b>{point.name}</b>: {point.y} = {point.percentage:.1f} %',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    }
                }
            },
            series: [{
                type: 'pie',
                name: 'Carreras'                
            }]
        });
        $("#tabla-datos").append("<?=$total_row;?>");
    });

    /*
                series: [{
                type: 'pie',
                name: 'Carreras',
                data: [
                    ['Firefox',   45.0],
                    ['IE',       26.8],
                    {
                        name: 'Chrome',
                        y: 12.8,
                        sliced: true,
                        selected: true
                    },
                    ['Safari',    8.5],
                    ['Opera',     6.2],
                    ['Others',   0.7]
                ]
            }]
    */
</script>

<?php
    }
?>