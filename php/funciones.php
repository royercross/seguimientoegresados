<?php
	function get_message($hide=false){
		if($hide===true) $classhide="alert-hide";
    	if(isset($_SESSION['mensaje'])){
			if(!isset($_SESSION['mensaje_tipo']))
				$_SESSION['mensaje_tipo']='success';
			echo "<div class='alert alert-".$_SESSION['mensaje_tipo']." ".$classhide."' >".$_SESSION['mensaje']."</div>";
		}
	    unset($_SESSION['mensaje']);
		unset($_SESSION['mensaje_tipo']);
	}
	
	function print_message($message,$tipo=NULL){
		if($tipo==NULL){$tipo='success';}
		echo "<div class='alert alert-".$tipo."' >".$message."</div>";
	}	
	
	function valida_email($email){   
		if(eregi("^([_a-z0-9-]+)(\.[_a-z0-9-]+)*@([a-z0-9-]+)(\.[a-z0-9-]+)*(\.[a-z]{2,4})$", $email))   
			return true;   
		else  
			return false;
	}
	
	function echo_secure($str){
		return htmlentities($str);
	}
?>