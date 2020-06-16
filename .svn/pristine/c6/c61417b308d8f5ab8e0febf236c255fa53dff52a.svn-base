<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/** 
 * @author putuariepra
 */

class Global_function {
	var $CI = NULL;
	function __construct(){
		$this->CI =& get_instance();
	}   
	
    function clean_array($data=array(), $clean=array()){        
        foreach ($clean as $value) {
            unset($data[$value]);
        }        
        return $data;
    }

    function add_array($data=array(), $add=array()){        
        foreach ($add as $key => $value) {
            $data[$key] = $value;
        }
        return $data;
    }

    function change_array($data=array(), $change=array()){        
        foreach ($change as $key => $value) {
            $data[$value] = $data[$key];
            unset($data[$key]);
        }
        return $data;
    }    
}
