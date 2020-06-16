<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_skpd extends CI_Model 
{
    var $table = 'm_skpd';

    public function __construct()
    {
        parent::__construct();           
    }

    function get_skpd($where = array()){		
		$this->db->from($this->table);		

		if (!empty($where)) {
			foreach ($where as $key => $value) {
				$this->db->where($key, $value);
			}			
		}

		$result = $this->db->get();
		return $result->result_array();
	}

	function get_one_skpd($where = array()){		
		$this->db->from($this->table);		

		if (!empty($where)) {
			foreach ($where as $key => $value) {
				$this->db->where($key, $value);
			}			
		}

		$result = $this->db->get();
		return $result->row();
	}

	function get_data_dropdown_skpd($where = array(), $all=FALSE, $first_null=FALSE){		
		$this->db->from($this->table);		

		if (!empty($where)) {
			foreach ($where as $key => $value) {
				$this->db->where($key, $value);
			}			
		}

		$result = $this->db->get();
		$data = $result->result();
		
		$result= array();
		
		if ($first_null) {
			$result[''] = "~~ Pilih SKPD ~~";
		}

		if ($all) {
			$result['all'] = "~~ Semua SKPD ~~";
		}		

		foreach ($data as $row) {
			$result[$row->id_skpd] = $row->kode_skpd .". ". $row->nama_skpd;
		}
		return $result;
	}

	function get_skpd_detail($where = array()){
		$this->db->select('m_skpd.*, m_bidkoordinasi.nama_koor');
		$this->db->from($this->table);
		$this->db->join('m_bidkoordinasi', 'm_bidkoordinasi.id_bidkoor=m_skpd.id_bidkoor', 'inner');

		if (!empty($where)) {
			foreach ($where as $key => $value) {
				$this->db->where($key, $value);
			}			
		}

		$result = $this->db->get();
		return $result;
	}

	function get_skpd_autocomplete($search){
		$this->db->select('id_skpd AS id, nama_skpd AS label');		
		$this->db->like('nama_skpd', $search);		
		$result = $this->db->get($this->table);
		return $result->result();
	} 

	function get_skpd_chosen(){
		$this->db->select('id_skpd AS id, nama_skpd AS label');		
		$result = $this->db->get($this->table);
		return $result->result();
	}   	
}
?>