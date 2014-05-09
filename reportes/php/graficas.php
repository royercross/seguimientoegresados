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
    if($grupo!="all"){
?>
      <dl class="tabs" data-tab>
        <dd class="active"><a href="#panel2-1">Tab 1</a></dd>
        <dd><a href="#panel2-2">Tab 2</a></dd>
        <dd><a href="#panel2-3">Tab 3</a></dd>
        <dd><a href="#panel2-4">Tab 4</a></dd>
      </dl>
      <div class="tabs-content">
        <div class="content active" id="panel2-1">
          <p>First panel content goes here...</p>
        </div>
        <div class="content" id="panel2-2">
          <p>Second panel content goes here...</p>
        </div>
        <div class="content" id="panel2-3">
          <p>Third panel content goes here...</p>
        </div>
        <div class="content" id="panel2-4">
          <p>Fourth panel content goes here...</p>
        </div>
      </div>
<?php
    }else{
        $panel="panel-grafica";
?>
        <div id="panel-grafica"></div>    
<?php
    }
?>

<?php
    // Construye los datos
    $query="SELECT a.*,COUNT(b.id_alumno) as total_alumnos FROM carreras a 
LEFT JOIN alumnos b ON a.id_carrera=b.id_carrera 
WHERE a.id_facultad=? AND a.status=1 
GROUP BY a.id_carrera";
    $mysql->execute($query,array($_SESSION['id_facultad']));   
    $data=array();   
    while($row=$mysql->getRow()){ 
        $data[]=sprintf("['%s', %d]",$row['nombre_carrera'], $row['total_alumnos']);
    }
    $data = implode(",",$data);
?>
<script>
	$(document).ready(function(){

        $('#grafica').foundation();
        $('#<?php echo $panel; ?>').highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Browser market shares at a specific website, 2014'
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
                        format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                        style: {
                            color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                        }
                    }
                }
            },
            series: [{
                type: 'pie',
                name: 'Carreras',
                data: [<?php echo $data; ?>]
            }]
        });
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