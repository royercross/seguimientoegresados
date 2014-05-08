<?php
  include("php/mysqlpdo.php");  
  require_once("config.inc.php"); 

  $mysql = new DBMannager();    
  $mysql->connect();  
  $query="SELECT id_login_intento,intentos FROM login_intentos WHERE ip = ?";
  $mysql->execute($query,array($_SERVER['REMOTE_ADDR']));
  $intentos=0;
  $error=false;$captcha=false;

  if($mysql->count() > 0){
    $intentos = $mysql->getRow();
    $id_intento = $intentos['id_login_intento'];
    $intentos = $intentos['intentos'];
  } 
  $recapcha=false;
  $recaptchaValido=true;
  if($intentos >= 3 ){
    $recapcha=true;   
    if(isset($_POST["recaptcha_challenge_field"])){
      require_once('php/recaptchalib.php');
      $privatekey = "6LfFi-gSAAAAABfybxqDFJsxiQXzfKWA_wsPxXxG";
      $resp = recaptcha_check_answer ($privatekey,
                    $_SERVER["REMOTE_ADDR"],
                    $_POST["recaptcha_challenge_field"],
                    $_POST["recaptcha_response_field"]);    
      if(!$resp->is_valid){
        $recaptchaValido=false;
        $error=2;
      }
    }
  }

  if($recaptchaValido && isset($_POST['txtUsuario']) && isset($_POST['txtPassword'])){

      $usuario = $_POST['txtUsuario'];
      $password = $_POST['txtPassword'];
      
      $query="SELECT * FROM usuarios WHERE usuario=? AND password=?";
      $mysql->execute($query,array($usuario,hash('sha256',$password)));
      
      if($mysql->count() > 0){
        $rs = $mysql->getRow();
        if($rs['usuario'] == $usuario){
          session_start();
          $_SESSION['id_usuario']=$rs['id_usuario'];              
          $_SESSION['id_facultad']=$rs['id_facultad'];        
          $_SESSION['TOKEN']='0a287b25c3570b784675e3aa3ef07892';                  
          $_SESSION["REMOTE_IP"]=$_SERVER['REMOTE_ADDR'];
          $query="UPDATE login_intentos SET intentos=0, ultimo_intento=NOW() WHERE id_login_intento=?";
          $mysql->execute($query,array($id_intento));         
          header("Location: inicio.php");         
        }
      }else{
        $error=1;
        $intentos++;
        if($intentos == 1){
          $query="INSERT INTO login_intentos(ip,intentos,ultimo_intento) VALUES (?,?,NOW())";
          $mysql->execute($query,array($_SERVER['REMOTE_ADDR'],1));         
        }else{
          $query="UPDATE login_intentos SET intentos=?, ultimo_intento=NOW() WHERE id_login_intento=?";
          $mysql->execute($query,array($intentos,$id_intento));
        }
      }
  } 
?>
<?php include("encabezado.php"); ?>

    <section class="row login-wrapper">
      <div class="panel">          
          <form class="" method="post" action="" data-abide>
            <?php if($error==1){ ?>
              <div class="alert-box alert">El usuario o contraseña no es valido.<a href="#" class="close">&times;</a></div>
            <?php } ?>  
            <?php if($error==2){ ?>
              <div class="alert-box alert">El captcha introducido no es valido.<a href="#" class="close">&times;</a></div>
            <?php } ?>             
            <fieldset>          
              <legend>Acceso</legend>              
              <div class="input-wrapper">
                <input name="txtUsuario" type="text" placeholder="usuario" required>
                <small class="error">El usuario es requerido.</small>
              </div>
              <div class="input-wrapper">
                <input name="txtPassword" type="password" class="" placeholder="contraseña" required>
                <small class="error">La contraseña es requerida.</small>
              </div>
              <?php if($recapcha && false){ ?>
                <div class="control-group">
                    <label class="control-label"></label>
                    <div class="controls">
                        <a id="recaptcha_image" href="#" class="thumbnail"></a>
                        <div class="recaptcha_only_if_incorrect_sol" style="color:red">Incorrecta intenta de nuevo.</div>
                    </div>
                </div>
                <div class="control-group">
                   <label class="recaptcha_only_if_image control-label">Ingresa las palabras de arriba:</label>
                  <label class="recaptcha_only_if_audio control-label">Ingresa los numeros que escuches:</label>
                
                  <div class="controls">
                      <div class="input-append">
                          <input type="text" id="recaptcha_response_field" name="recaptcha_response_field" class="input-recaptcha" required/>
                          <a class="btn" href="javascript:Recaptcha.reload()"><i class="icon-refresh"></i></a>
                          <a class="btn recaptcha_only_if_image" href="javascript:Recaptcha.switch_type('audio')"><i title="Get an audio CAPTCHA" class="icon-headphones"></i></a>
                          <a class="btn recaptcha_only_if_audio" href="javascript:Recaptcha.switch_type('image')"><i title="Get an image CAPTCHA" class="icon-picture"></i></a>
                        <a class="btn" href="javascript:Recaptcha.showhelp()"><i class="icon-question-sign"></i></a>
                      </div>
                  </div>
              </div>  
              <?php } ?>
               <button type="submit">Submit</button>
             </fieldset>
          </form>
      </div>
    </section>    
 <script type="text/javascript">
     var RecaptchaOptions = {
        theme : 'custom',
        custom_theme_widget: 'recaptcha_widget'
     };
</script>  
<script type="text/javascript" src="https://www.google.com/recaptcha/api/challenge?k=6LfFi-gSAAAAAMZ1J3d5YnLVxLnXrHR8MYChZyPN"></script>        
<?php include("piepagina.php"); ?>