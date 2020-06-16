<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_kegiatan extends CI_Model 
{
       
    public function __construct()
    {
        parent::__construct();           
    }
    
    var $table_kegiatan = 'm_kegiatan';

	function get_value_autocomplete_kd_keg($search, $kd_urusan, $kd_bidang, $kd_prog){
		$this->db->select('Kd_Keg AS id, Ket_Kegiatan AS label');
		$this->db->where('Kd_Urusan', $kd_urusan);
		$this->db->where('Kd_Bidang', $kd_bidang);
		$this->db->where('Kd_Prog', $kd_prog);
		$this->db->where('(Kd_Keg LIKE \'%'. $search .'%\' OR Ket_Kegiatan LIKE \'%'. $search .'%\')');
		$result = $this->db->get($this->table_kegiatan);
		return $result->result();
	}

	function get_keg($kd_urusan=NULL, $kd_bidang=NULL, $kd_prog=NULL){
		$this->db->select('Kd_Keg AS id, Ket_Kegiatan AS nama');

		if (!empty($kd_urusan)) {
			$this->db->where('Kd_Urusan', $kd_urusan);
		}
		if (!empty($kd_bidang)) {
			$this->db->where('Kd_Bidang', $kd_bidang);		
		}
		if (!empty($kd_prog)) {
			$this->db->where('Kd_Prog', $kd_prog);		
		}

		$result = $this->db->get($this->table_kegiatan);
		return $result->result();
	}    	
}
?>