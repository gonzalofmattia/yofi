<?php

class varFunctions {
	var $POSTS  	= "nada"; 	// Inicio de
	var $GETS 	= "nada"; 	// variables
	var $SESSIONS 	= "nada"; 	// variables

	Function Inicializar($VAR_POST,$VAR_GET,$VAR_SESSION){ 
		$this->GETS 	= $VAR_GET;
		$this->POSTS 	= $VAR_POST ;
		$this->SESSIONS = $VAR_SESSION ;
	}

	Function TraerGet($strbusca){
		if ( isset($this->GETS[$strbusca]) == false ) {
			$salida = '' ;
		} else {
			$salida = $this->GETS[$strbusca] ;
		}
		return($salida) ;
	}

	Function TraerPost($strbusca){
		if ( isset($this->POSTS[$strbusca]) == false ) {
			$salida = '' ;
		} else {
			$salida = $this->POSTS[$strbusca] ;
		}
		return($salida) ;
	}
	
	Function TraerSession($strbusca){
		if ( isset($this->SESSIONS[$strbusca]) == false ) {
			$salida = '' ;
		} else {
			$salida = $this->SESSIONS[$strbusca] ;
		}
		return($salida) ;
	}
	
	Function PonerSession($strbusca,$strvalor){
		if ( isset($this->SESSIONS[$strbusca]) == false ) {
			$this->SESSIONS[$strbusca] = $strvalor;
		}
	}
		
	Function TraerVariable($strbusca){
		if ( isset($this->POSTS[$strbusca]) == false ) {
			$salida = '' ;
		} else {
			$salida = $this->POSTS[$strbusca] ;
			echo("<br> sale post");
		}
		return($salida) ;
	}
}

?>