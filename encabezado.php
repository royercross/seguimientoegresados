<!doctype html>
<html class="no-js" lang="en">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Foundation | Welcome</title>
    <link rel="stylesheet" href="css/normalize.css" />
    <link rel="stylesheet" href="css/foundation.min.css" />
    <link rel="stylesheet" href="css/style.css" />
    <script src="lib/modernizr.js"></script>
  </head>
  <body>
    <header>
      <section class="row header-bg">
        <section class="medium-3 large-3 columns">
          <a href="/" class="logo">Universidad Autonoma de Sinaloa</a>
        </section>
        <section class="medium-9 large-9 columns">
            <h3 class="titulo-1">UNIVERSIDAD AUTÓNOMA DE SINALOA</h3>
            <h2 class="titulo-2">SECRETARÍA ACADÉMICA UNIVERSITARIA</h2>            
            <span class="titulo-3">ESTUDIO INSTITUCIONAL DE SEGUIMIENTO DE EGRESADOS(PISE)<BR />UNIDAD REGIONAL</span>      
            <?php 
                $dias=array("","Lunes","Martes","Miercoles","Jueves","Viernes","Sabado","Domingo");
                $meses=array("","Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
             ?>
            <span class="fecha"><?php echo $dias[date('N')].", ".date('j')." de ".$meses[date('n')]." de ".date('Y'); ?></span>          
        </section>
      </section>
    </header>