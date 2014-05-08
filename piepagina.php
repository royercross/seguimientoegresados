    
    <?php if(!isset($loadScripts) || $loadScripts){ ?>
    <script src="<?=$ruta;?>lib/jquery.js"></script>
    <script src="<?=$ruta;?>lib/foundation.min.js"></script>
    <script src="<?=$ruta;?>lib/foundation.abide.js"></script>
    <script src="<?=$ruta;?>lib/foundation.alert.js"></script>
    <?php } ?>
    <script>
      $(document).foundation();
    </script>
  </body>
</html>