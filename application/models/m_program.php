<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_program extends CI_Model 
{
       
    public function __construct()
    {
        parent::__construct();           
    }
    
    var $table_program = 'm_program';

	function get_value_autocomplete_kd_prog($search, $kd_urusan, $kd_bidang){
		$this->db->select('Kd_Prog AS id, Ket_Program AS label');
		$this->db->where('Kd_Urusan', $kd_urusan);
		$this->db->where('Kd_Bidang', $kd_bidang);
		$this->db->where('(Kd_Prog LIKE \'%'. $search .'%\' OR Ket_Program LIKE \'%'. $search .'%\')');
		$result = $this->db->get($this->table_program);
		return $result->result();
	}	

	function get_prog($kd_urusan=NULL, $kd_bidang=NULL){
		$this->db->select('Kd_Prog AS id, Ket_Program AS nama');
		if (!empty($kd_urusan)) {
			$this->db->where('Kd_Urusan', $kd_urusan);
		}
		if (!empty($kd_bidang)) {
			$this->db->where('Kd_Bidang', $kd_bidang);		
		}		
		$result = $this->db->get($this->table_program);
		return $result->result();
	}
}
?>