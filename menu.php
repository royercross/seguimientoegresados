<?php require_once("config.inc.php"); ?>
<div class="top-bar" data-topbar>
  <ul class="title-area">
    <li class="name">
      <h1><a href="#">SeguimientoEgresados</a></h1>
    </li>
     <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
    <li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
  </ul>
  <section class="top-bar-section">
    <ul class="left">
      <li><a href="<?=$ruta;?>alumnos/modulo_alumnos.php">Alumnos</a></li>    
      <li><a href="<?=$ruta;?>reportes/modulo_reportes.php">Reportes</a></li>
      <li><a href="<?=$ruta;?>logout.php">Salir</a></li>                
    </ul>
  </section>
</div>