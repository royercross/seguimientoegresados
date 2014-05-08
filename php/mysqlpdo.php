<?php

class DBMannager
{
    var $server="localhost";
    var $username="root";
    var $password="";
	var $database="segegresados";
    var $db_selected;
    var $connection;   
	var $result;
	var $desarrollo=true;
	var $index=-1;

	function __construct() {}
	
    function connect()
	{		
		try{
			// Abre la caonexion a la base de datos
			$this->connection = new PDO("mysql:host=".$this->server.";dbname=".$this->database.";charset=utf8;", $this->username, $this->password);
			//$this->connection->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, TRUE);
			$this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}catch (PDOException $e) {
			if($this->desarrollo)
				die("<p>Error: " . $e->getMessage() . "</p>\n");
			else
				die("Error de Conexion");
	    }		
		$this->connection->exec("SET NAMES 'utf8'");
	}
	
	function exec($query){
		return $this->connection->exec($query);
	}
		
    function execute($query,$parameters=NULL)
    {
		try{		
			$this->index=-1;	
			if($parameters!=NULL){
				$preparedQuery=$this->connection->prepare($query);
				$preparedQuery->execute($parameters);
				try{
					$this->result = $preparedQuery->fetchAll();
				}catch(PDOException $ex){}
			}else{
				//$preparedQuery=$this->connection->prepare($query);	
				$this->result = $this->connection->query($query);	
	
			}
		}catch(PDOException $ex){
			//
			if($this->desarrollo)
				echo $ex->getMessage();
		}
		/*
        if (!$this->result) {
            die('Could not run query: ' . mysql_error());
			$this->close();
			//die('Error de acceso.');
		}
		*/
		return $this->result;
    }
	
	function count(){
		if(is_array($this->result))
			return count($this->result);
		else
			return $this->result->rowCount();
	}
	function getArray(){
		if(is_array($this->result))
			return $this->result;
		else
			return $this->result->fetchAll();		
	}
	
	function getRow(){
		if(is_array($this->result)){							
				$this->index++;
				if($this->index >= count($this->result)) return false;
				return $this->result[$this->index];

		}else
			return $this->result->fetch();
	}
	
	function getlastInsertedId(){
		return $this->connection->lastInsertId();
	}
	
    function close()
    {
		$this->connection = null;
    }

	//***** funcion que cambia el formato de la fecha de 2000-01-01 a 01/01/2000
	function normaldate($fecha){
		$mifecha=explode("-",$fecha);
		return $mifecha[2]."/".$mifecha[1]."/".$mifecha[0];
	}


	//***** funcion que cambia el formato de la fecha de 01/01/2000 a 2000-01-01
	function mysqldate($fecha){
		$mifecha=explode("/",$fecha);
		return $mifecha[2]."-".$mifecha[1]."-".$mifecha[0];
	} 	
	
} 


?>
