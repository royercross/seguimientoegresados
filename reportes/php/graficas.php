<?php require_once("../../check.php"); ?> 


<?php  

    if( !isset($_POST['carrera']) || !isset($_POST['generacion']) || !isset($_POST['grupo']) )
        die();

    require_once("../../php/mysqlpdo.php");  
    $mysql = new DBMannager();    
    $mysql->connect();   

    $carrera=$_POST['carrera'];
    $generacion=$_POST['generacion'];
    $grupo=$_POST['grupo'];
        
    $panel="";
    $preguntas = array();
    if($grupo!="all"){
        switch($grupo){
            case 2: $preguntas_id = array(7,8,9); break;
        }

        $query="SELECT * FROM c_preguntas WHERE id_pregunta IN (".implode(",",$preguntas_id).") ";
        //$mysql->execute($query,array($_SESSION['id_facultad']));   
        $mysql->execute($query);   
        $preguntas = $mysql->getArray();
        for($i=0; $i < count($preguntas); $i++){            
            $query="SELECT * FROM c_opciones_multiples WHERE id_pregunta = ?";
            $mysql->execute($query,array($preguntas[$i]['id_pregunta']));   

            $respuestas = $mysql->getArray();
            $temporal = array();
            foreach($respuestas as $respuesta){
                $temporal[$respuesta['valor']] = $respuesta['opcion'];
            }
            $preguntas[$i]['opciones'] = $temporal;
            //var_dump($respuestas);                    
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
            foreach($preguntas as $pregunta){

                $query="SELECT * FROM c_respuestas a INNER JOIN alumnos b ON a.id_alumno=b.id_alumno WHERE b.id_facultad=? AND a.id_pregunta IN (".implode(",",$preguntas_id).")";
                $mysql->execute($query,array($_SESSION['id_facultad']));                   
                $respuestas = $mysql->getArray();
        ?>                
            <div class="content <?php if($first){$first=false;echo 'active';}?>" id="panel-<?=$cont;?>">
                <div class="rows">
                    <section class="small-12 large-3 columns no-padding">
                        <table id="tabla-datos-<?=$cont;?>" class="tabla-datos">
                            <thead>
                                <th></th>
                                <th>Alumno</th>
                                <th>%</th>
                            </thead>
                            <tbody> 
                                <?php foreach($pregunta['opciones'] as $opcion){ ?>
                                    <tr>
                                        <td><?=$opcion;?></td>
                                    </tr>                                    
                                <?php } ?>
                            </tbody>
                        </table>
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
                    text: 'Alumnos por Carrera'
                },
                tooltip: {
                  pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
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
WHERE a.id_facultad=? AND a.status=1 
GROUP BY a.id_carrera";
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
            tooltip: {
              pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
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