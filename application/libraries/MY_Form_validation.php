<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Form_validation extends CI_Form_validation {
    
	function get_error_array()
	{
		return $this->_error_array;
	}

} 
