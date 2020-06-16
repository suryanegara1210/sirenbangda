<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_rpjmd_trx extends CI_Model 
{	
	var $table = 't_rpjmd';
	var $table_misi = 't_rpjmd_misi';
	var $table_tujuan = 't_rpjmd_tujuan';
	var $table_sasaran = 't_rpjmd_sasaran';
	var $table_indikator = 't_rpjmd_indikator';
	var $table_program = 't_rpjmd_program';
	var $table_kebijakan_sasaran = 't_rpjmd_kebijakan_sasaran';

    public function __construct()
    {
        parent::__construct();           
    }    
	
	private function create_history($data){		
		$data['created_date'] = date("Y-m-d H:i:s");
		$data['created_by'] = $this->session->userdata('username');
		return $data;
	}

	private function change_history($data){		
		$data['changed_date'] = date("Y-m-d H:i:s");
		$data['changed_by'] = $this->session->userdata('username');
		return $data;
	}

	private function add_misi($misi){
		$this->db->insert($this->table_misi, $misi);
		return $this->db->insert_id();
	}

	private function update_misi($misi, $id){
		$this->db->where("id", $id);
		$this->db->update($this->table_misi, $misi);
	}

	private function add_tujuan($tujuan){
		$this->db->insert_batch($this->table_tujuan, $tujuan);
	}

	private function update_tujuan($tujuan, $id){
		$this->db->where("id", $id);
		$this->db->update($this->table_tujuan, $tujuan);
	}

	function get_all_rpjmd(){
		$query = "SELECT * FROM ".$this->table;
		$result = $this->db->query($query);
		return $result->result();
	}

	function get_one_rpjmd($id){
		$this->db->select($this->table.".*");
		$this->db->from($this->table);
		$this->db->where($this->table.".id", $id);		

		$result = $this->db->get();
		return $result->row();
	}

	function get_all_rpjmd_misi($id, $no_result=FALSE){
		$this->db->from($this->table_misi);		
		$this->db->where("id_rpjmd", $id);
		$result = $this->db->get();
		if ($no_result) {
			return $result;
		}else{
			return $result->result();
		}		
	}

	function get_all_rpjmd_tujuan($id_rpjmd, $no_result=FALSE){
		$this->db->from($this->table_tujuan);		
		$this->db->where("id_rpjmd", $id_rpjmd);
		$result = $this->db->get();
		if ($no_result) {
			return $result;
		}else{
			return $result->result();
		}	
	}

	function get_each_rpjmd_tujuan($id, $id_misi){
		$this->db->from($this->table_tujuan);		
		$this->db->where("id_rpjmd", $id);
		$this->db->where("id_misi", $id_misi);
		$result = $this->db->get();
		return $result;		
	}

	function get_one_rpjmd_tujuan($id_rpjmd, $id_tujuan){
		$this->db->from($this->table_tujuan);
		$this->db->where("id_rpjmd", $id_rpjmd);
		$this->db->where("id", $id_tujuan);
		$result = $this->db->get();
		return $result->row();
	}

	function add_rpjmd($data, $misi, $tujuan){
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();
		
		$data = $this->create_history($data);

		$this->db->insert($this->table, $data);
		$id_rpjmd = $this->db->insert_id();

		$id_misi = array();		
		foreach ($misi as $key => $value) {			
			$id_misi[$key] = $this->add_misi(array('id_rpjmd' => $id_rpjmd, 'misi' => $value));
		}

		$tujuan_batch = array();
		foreach ($misi as $key => $value) {
			foreach ($tujuan[$key] as $key1 => $value1) {
				$tujuan_batch[] = array('id_rpjmd' => $id_rpjmd, 'id_misi' => $id_misi[$key], 'tujuan' => $value1);				
			}
		}		
		
		$this->add_tujuan($tujuan_batch);

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	function edit_rpjmd($data, $misi, $tujuan, $id_misi_old, $id_tujuan, $id_rpjmd){
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();
		
		$data = $this->change_history($data);

		$this->db->where('id', $id_rpjmd);
		$this->db->update($this->table, $data);		
		
		$id_misi = array();
		foreach ($misi as $key => $value) {			
			if (!empty($id_misi_old[$key])) {
				$this->update_misi(array('id_rpjmd' => $id_rpjmd, 'misi' => $value), $id_misi_old[$key]);
				$id_misi[$key] = $id_misi_old[$key];
				unset($id_misi_old[$key]);
			}else{
				$id_misi[$key] = $this->add_misi(array('id_rpjmd' => $id_rpjmd, 'misi' => $value));
			}
		}		

		//tujuan batch untuk yg baru
		$tujuan_batch = array();
		foreach ($misi as $key => $value) {
			foreach ($tujuan[$key] as $key1 => $value1) {
				if (!empty($id_tujuan[$key][$key1])) {
					//update tujuannya
					$this->update_tujuan(array('tujuan' => $value1, 'id_misi' => $id_misi[$key]), $id_tujuan[$key][$key1]);
					unset($id_tujuan[$key][$key1]);
				}else{
					$tujuan_batch[] = array('id_rpjmd' => $id_rpjmd, 'id_misi' => $id_misi[$key], 'tujuan' => $value1);
				}			
			}
		}				

		if (!empty($tujuan_batch)) {
			$this->add_tujuan($tujuan_batch);
		}		

		
		$id_tujuan_batch = array();
		foreach ($misi as $key => $value) {
			foreach ($id_tujuan[$key] as $key1 => $value1) {
				$id_tujuan_batch[] = $value1;
			}
		}

		if (!empty($id_tujuan_batch)) {
			$this->db->where_in('id', $id_tujuan_batch);
			$this->db->delete($this->table_tujuan);			
		}

		if (!empty($id_misi_old)) {
			$this->db->where_in('id', $id_misi_old);
			$this->db->delete($this->table_misi);			
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}	

	function delete_rpjmd($id){
		$this->db->where('id', $id);
		$result = $this->db->delete($this->table);
		return $result;
	}

	function get_all_sasaran($id_rpjmd, $id_tujuan=NULL){
		$this->db->select($this->table_sasaran.".*");
		$this->db->where('id_rpjmd', $id_rpjmd);
		if (!empty($id_tujuan)) {
			$this->db->where('id_tujuan', $id_tujuan);
		}		
		$this->db->from($this->table_sasaran);		

		$result = $this->db->get();
		return $result->result();
	}

	function get_kebijakan_sasaran($id_sasaran, $return_result=TRUE){
		$this->db->where('id_sasaran', $id_sasaran);
		$this->db->from($this->table_kebijakan_sasaran);
		$result = $this->db->get();
		if ($return_result) {
			return $result->result();
		}else{
			return $result;
		}
	}

	function get_one_sasaran($id_rpjmd=NULL, $id_tujuan=NULL, $id_sasaran){
		if (!empty($id_rpjmd)) {
			$this->db->where('id_rpjmd', $id_rpjmd);
		}
		
		if (!empty($id_tujuan)) {
			$this->db->where('id_tujuan', $id_tujuan);
		}

		$this->db->where('id', $id_sasaran);
		$this->db->from($this->table_sasaran);	
		$result = $this->db->get();
		return $result->row();
	}

	function add_sasaran($data, $indikator){
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();

		$result = $this->db->insert($this->table_sasaran, $data);

		$id_sasaran = $this->db->insert_id();
		foreach ($indikator as $key => $value) {
			$this->db->insert($this->table_kebijakan_sasaran, array('id_sasaran' => $id_sasaran, 'kebijakan' => $value));
		}

		$this->db->trans_complete();
		return $this->db->trans_status();		
	}

	function edit_sasaran($data, $id_sasaran, $kebijakan, $id_kebijakan){
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();

		$this->db->where('id', $id_sasaran);
		$result = $this->db->update($this->table_sasaran, $data);		

		foreach ($kebijakan as $key => $value) {			
			if (!empty($id_kebijakan[$key])) {
				$this->db->where('id', $id_kebijakan[$key]);
				$this->db->where('id_sasaran', $id_sasaran);
				$this->db->update($this->table_kebijakan_sasaran, array('kebijakan' => $value));
				unset($id_kebijakan[$key]);
			}else{
				$this->db->insert($this->table_kebijakan_sasaran, array('id_sasaran' => $id_sasaran, 'kebijakan' => $value));
			}
		}

		if (!empty($id_kebijakan)) {
			$this->db->where_in('id', $id_kebijakan);
			$this->db->delete($this->table_kebijakan_sasaran);			
		}

		$this->db->trans_complete();
		return $this->db->trans_status();		
	}

	function delete_sasaran($id){
		$this->db->where('id', $id);		
		$result = $this->db->delete($this->table_sasaran); 
		return $result;
	}

	function get_all_indikator($id_rpjmd, $id_sasaran, $with_satuan){
		$this->db->select($this->table_indikator.".*");
		$this->db->where('id_rpjmd', $id_rpjmd);
		$this->db->where('id_sasaran', $id_sasaran);		
		$this->db->from($this->table_indikator);

		if ($with_satuan) {
			$this->db->select("m_lov.nama_value");
			$this->db->join("m_lov",$this->table_indikator.".satuan_target = m_lov.kode_value AND kode_app='1'","inner");
		}

		$result = $this->db->get();
		return $result->result();
	}

	function get_one_indikator($id_rpjmd=NULL, $id_sasaran=NULL, $id_indikator){
		if (!empty($id_rpjmd)) {
			$this->db->where('id_rpjmd', $id_rpjmd);
		}
		
		if (!empty($id_sasaran)) {
			$this->db->where('id_sasaran', $id_sasaran);
		}

		$this->db->where('id', $id_indikator);
		$this->db->from($this->table_indikator);	
		$result = $this->db->get();
		return $result->row();
	}

	function get_info_tujuan_n_sasaran_n_indikator($id_tujuan=NULL, $id_sasaran=NULL){
		$this->db->select($this->table_tujuan.".tujuan");
		$this->db->from($this->table_tujuan);
		if (!empty($id_tujuan)) {
			$this->db->where('id_tujuan', $id_tujuan);
		}
		if (!empty($id_sasaran)) {
			$this->db->select($this->table_sasaran.".sasaran");
			$this->db->join($this->table_sasaran,$this->table_sasaran.".id_tujuan = ". $this->table_tujuan .".id","inner");			
			$this->db->where($this->table_sasaran.'.id', $id_sasaran);
		}		

		$result = $this->db->get();
		return $result->row();
	}

	function add_indikator($data){				
		$result = $this->db->insert($this->table_indikator, $data);
		return $result;
	}

	function edit_indikator($data, $id_indikator){		
		$this->db->where('id', $id_indikator);
		$result = $this->db->update($this->table_indikator, $data);	

		return $result;
	}

	function delete_indikator($id){		
		$this->db->where('id', $id);		
		$result = $this->db->delete($this->table_indikator); 		
		return $result;
	}

	function get_all_program($id_rpjmd, $id_sasaran, $id_indikator){
		$this->db->select($this->table_program.".id AS id_program_rpjmd");
		$this->db->select("t_renstra_prog_keg.*");
		$this->db->select("Nm_Bidang");
		$this->db->select("nama_skpd");
		$this->db->where($this->table_program.'.id_rpjmd', $id_rpjmd);
		$this->db->where($this->table_program.'.id_sasaran', $id_sasaran);
		$this->db->where($this->table_program.'.id_indikator', $id_indikator);		
		$this->db->from($this->table_program);		
		$this->db->join('t_renstra_prog_keg',"t_renstra_prog_keg.id = ". $this->table_program .".id_program_renstra AND t_renstra_prog_keg.is_prog_or_keg=1","inner");
		$this->db->join('t_renstra', "t_renstra_prog_keg.id_renstra = t_renstra.id","inner");
		$this->db->join("m_skpd", "t_renstra.id_skpd = m_skpd.id_skpd","inner");
		$this->db->join("m_bidang", "t_renstra_prog_keg.kd_urusan = m_bidang.Kd_Urusan AND t_renstra_prog_keg.kd_bidang = m_bidang.Kd_Bidang","inner");
		$result = $this->db->get();
		return $result->result();
	}

	function get_all_pilih_program($search, $start, $length, $order, $order_arr, $id_rpjmd, $id_sasaran, $id_indikator){		
		$query = "SELECT * 
							FROM (
								SELECT 
									t_renstra_prog_keg.id, 
									t_renstra_prog_keg.kd_urusan, 
									t_renstra_prog_keg.kd_bidang, 
									t_renstra_prog_keg.kd_program,
									nama_prog_or_keg, 
									m_skpd.nama_skpd, 
									Nm_Bidang, 
									t_rpjmd_program.id_rpjmd, 
									t_rpjmd_program.id_indikator, 
									t_rpjmd_program.id_sasaran 
								FROM 
									(SELECT * FROM t_renstra_prog_keg WHERE is_prog_or_keg=1 AND (id_status=4 OR id_status=7)) AS t_renstra_prog_keg 
								INNER JOIN 
									t_renstra ON t_renstra_prog_keg.id_renstra=t_renstra.id 
								INNER JOIN 
									m_skpd ON m_skpd.id_skpd=t_renstra.id_skpd 
								INNER JOIN 
									m_bidang ON m_bidang.Kd_Urusan=t_renstra_prog_keg.kd_urusan AND m_bidang.Kd_Bidang=t_renstra_prog_keg.kd_bidang 
								LEFT JOIN 
									(SELECT * FROM t_rpjmd_program GROUP BY id_rpjmd, id_sasaran, id_program_renstra) AS t_rpjmd_program ON t_renstra_prog_keg.id=t_rpjmd_program.id_program_renstra
							) AS vw1 
							WHERE 
								(nama_prog_or_keg LIKE ? OR Nm_Bidang LIKE ? OR nama_skpd LIKE ?) AND 
								((id_rpjmd IS NULL AND id_sasaran IS NULL AND id_indikator IS NULL)) 
							ORDER BY ? ? 
							LIMIT ".$start.", ".$length.";";
		$data = array("%". $search['value'] ."%", "%". $search['value'] ."%", "%". $search['value'] ."%", $id_rpjmd, $id_sasaran, $id_indikator, $order_arr[$order["column"]], $order["dir"]);
		$result = $this->db->query($query, $data);
		return $result->result();
	}

	function count_all_pilih_program($search, $start, $length, $order, $order_arr, $id_rpjmd, $id_sasaran, $id_indikator){		
		$query = "SELECT COUNT(*) AS count
							FROM (
								SELECT 
									t_renstra_prog_keg.id, 
									t_renstra_prog_keg.kd_urusan, 
									t_renstra_prog_keg.kd_bidang, 
									t_renstra_prog_keg.kd_program,
									nama_prog_or_keg, 
									m_skpd.nama_skpd, 
									Nm_Bidang, 
									t_rpjmd_program.id_rpjmd, 
									t_rpjmd_program.id_indikator, 
									t_rpjmd_program.id_sasaran 
								FROM 
									(SELECT * FROM t_renstra_prog_keg WHERE is_prog_or_keg=1 AND (id_status=4 OR id_status=7)) AS t_renstra_prog_keg 
								INNER JOIN 
									t_renstra ON t_renstra_prog_keg.id_renstra=t_renstra.id 
								INNER JOIN 
									m_skpd ON m_skpd.id_skpd=t_renstra.id_skpd 
								INNER JOIN 
									m_bidang ON m_bidang.Kd_Urusan=t_renstra_prog_keg.kd_urusan AND m_bidang.Kd_Bidang=t_renstra_prog_keg.kd_bidang 
								LEFT JOIN 
									(SELECT * FROM t_rpjmd_program GROUP BY id_rpjmd, id_sasaran, id_program_renstra) AS t_rpjmd_program ON t_renstra_prog_keg.id=t_rpjmd_program.id_program_renstra
							) AS vw1 
							WHERE 
								(nama_prog_or_keg LIKE ? OR Nm_Bidang LIKE ? OR nama_skpd LIKE ?) AND 
								((id_rpjmd IS NULL AND id_sasaran IS NULL AND id_indikator IS NULL) OR (id_rpjmd=? AND id_sasaran=? AND id_indikator!=?)) 
							";
		$data = array("%". $search['value'] ."%", "%". $search['value'] ."%", "%". $search['value'] ."%", $id_rpjmd, $id_sasaran, $id_indikator);
		$result = $this->db->query($query, $data);
		$result = $result->row();
		return $result->count;		
	}

	function add_program($data){				
		$result = $this->db->insert($this->table_program, $data);
		return $result;
	}

	function delete_program($id){
		$this->db->where('id', $id);		
		$result = $this->db->delete($this->table_program); 		
		return $result;
	}

	function preview_program_rpjmd($id){
		$query = "SELECT t_renstra_prog_keg.* 
					FROM 
						t_rpjmd_program 
					INNER JOIN 
						(SELECT 
							t_prog.*, 
							m_skpd.nama_skpd, 
							m_bidang.Nm_Bidang AS nm_bidang, 
							m_lov.nama_value, 
							SUM(t_keg.nominal_1) AS nominal_1_pro, 
							SUM(t_keg.nominal_2) AS nominal_2_pro, 
							SUM(t_keg.nominal_3) AS nominal_3_pro, 
							SUM(t_keg.nominal_4) AS nominal_4_pro, 
							SUM(t_keg.nominal_5) AS nominal_5_pro 
						FROM 
							t_renstra_prog_keg AS t_prog 
						INNER JOIN 
							t_renstra_prog_keg AS t_keg ON t_prog.id=t_keg.parent AND t_keg.is_prog_or_keg=2 
						INNER JOIN 
							t_renstra ON t_prog.id_renstra=t_renstra.id 
						INNER JOIN 
							m_skpd ON m_skpd.id_skpd=t_renstra.id_skpd 
						INNER JOIN 
							m_bidang ON m_bidang.Kd_Urusan=t_prog.kd_urusan AND m_bidang.Kd_Bidang=t_prog.kd_bidang 
						INNER JOIN 
							m_lov ON t_prog.satuan_target=m_lov.kode_value AND kode_app='1'
						WHERE 
							t_prog.is_prog_or_keg=1 
						GROUP BY 
							t_prog.id
						) AS t_renstra_prog_keg ON t_rpjmd_program.id_program_renstra=t_renstra_prog_keg.id 
					WHERE 
						t_rpjmd_program.id=?";
		$data = array($id);
		$result = $this->db->query($query, $data);
		return $result->row();		
	}

	function get_all_bidang_urusan_4_cetak_final(){
		$query = "SELECT m_urusan.*, m_bidang.* FROM t_rpjmd_program INNER JOIN t_renstra_prog_keg ON t_rpjmd_program.id_program_renstra=t_renstra_prog_keg.id INNER JOIN m_bidang ON (t_renstra_prog_keg.kd_urusan=m_bidang.Kd_Urusan AND t_renstra_prog_keg.kd_bidang=m_bidang.Kd_Bidang) INNER JOIN m_urusan ON t_renstra_prog_keg.kd_urusan=m_urusan.Kd_Urusan GROUP BY t_renstra_prog_keg.kd_urusan, t_renstra_prog_keg.kd_bidang";		
		$result = $this->db->query($query);
		return $result->result();
	}

	function get_bidang_urusan_4_cetak_final($urusan, $bidang){
		$query = "SELECT t_renstra_prog_keg.*, m_bidang.*,SUM(t_renstra_prog_keg.nominal_1) AS nominal_1_pro, SUM(t_renstra_prog_keg.nominal_2) AS nominal_2_pro, SUM(t_renstra_prog_keg.nominal_3) AS nominal_3_pro, SUM(t_renstra_prog_keg.nominal_4) AS nominal_4_pro, SUM(t_renstra_prog_keg.nominal_5) AS nominal_5_pro FROM t_rpjmd_program INNER JOIN t_renstra_prog_keg ON t_rpjmd_program.id_program_renstra=t_renstra_prog_keg.parent INNER JOIN m_bidang ON (t_renstra_prog_keg.kd_urusan=m_bidang.Kd_Urusan AND t_renstra_prog_keg.kd_bidang=m_bidang.Kd_Bidang) WHERE t_renstra_prog_keg.is_prog_or_keg=? AND t_renstra_prog_keg.kd_urusan=? AND t_renstra_prog_keg.kd_bidang=? GROUP BY t_renstra_prog_keg.kd_urusan, t_renstra_prog_keg.kd_bidang";
		$data = array(2, $urusan, $bidang);
		$result = $this->db->query($query, $data);		
		return $result->result();	
	}

	function get_bidang_urusan_skpd_4_cetak_final($urusan, $bidang){
		$query = "SELECT t_renstra_prog_keg.*,m_skpd.*,SUM(t_renstra_prog_keg.nominal_1) AS nominal_1_pro, SUM(t_renstra_prog_keg.nominal_2) AS nominal_2_pro, SUM(t_renstra_prog_keg.nominal_3) AS nominal_3_pro, SUM(t_renstra_prog_keg.nominal_4) AS nominal_4_pro, SUM(t_renstra_prog_keg.nominal_5) AS nominal_5_pro FROM t_rpjmd_program INNER JOIN t_renstra_prog_keg ON t_rpjmd_program.id_program_renstra=t_renstra_prog_keg.parent INNER JOIN t_renstra ON (t_renstra.id=t_renstra_prog_keg.id_renstra AND t_renstra_prog_keg.is_prog_or_keg=?) INNER JOIN m_skpd ON t_renstra.id_skpd=m_skpd.id_skpd WHERE t_renstra_prog_keg.is_prog_or_keg=? AND t_renstra_prog_keg.kd_urusan=? AND t_renstra_prog_keg.kd_bidang=? GROUP BY t_renstra_prog_keg.kd_urusan, t_renstra_prog_keg.kd_bidang, m_skpd.id_skpd";
		$data = array(2, 2, $urusan, $bidang);
		$result = $this->db->query($query, $data);
		return $result->result();	
	}

	function get_bidang_urusan_skpd_program_4_cetak_final($urusan, $bidang, $skpd){
		$query = "SELECT vw1.*,m_skpd.*,SUM(t_renstra_prog_keg.nominal_1) AS nominal_1_pro, SUM(t_renstra_prog_keg.nominal_2) AS nominal_2_pro, SUM(t_renstra_prog_keg.nominal_3) AS nominal_3_pro, SUM(t_renstra_prog_keg.nominal_4) AS nominal_4_pro, SUM(t_renstra_prog_keg.nominal_5) AS nominal_5_pro FROM t_rpjmd_program INNER JOIN t_renstra_prog_keg ON t_rpjmd_program.id_program_renstra=t_renstra_prog_keg.parent INNER JOIN t_renstra ON (t_renstra.id=t_renstra_prog_keg.id_renstra AND t_renstra_prog_keg.is_prog_or_keg=?) INNER JOIN m_skpd ON t_renstra.id_skpd=m_skpd.id_skpd INNER JOIN t_renstra_prog_keg AS vw1 ON (vw1.id=t_renstra_prog_keg.parent AND vw1.is_prog_or_keg=?) WHERE t_renstra_prog_keg.is_prog_or_keg=? AND t_renstra_prog_keg.kd_urusan=? AND t_renstra_prog_keg.kd_bidang=? AND t_renstra.id_skpd=? GROUP BY t_renstra_prog_keg.kd_urusan, t_renstra_prog_keg.kd_bidang, m_skpd.id_skpd, t_renstra_prog_keg.kd_program";				
		$data = array(2, 1, 2, $urusan, $bidang, $skpd);
		$result = $this->db->query($query, $data);		
		return $result->result();	
	}

	function get_misi_rpjmd_4_cetak_final(){
		$query = "SELECT * FROM t_rpjmd_misi";
		$result = $this->db->query($query);
		return $result->result();		
	}

	function get_tujuan_rpjmd_4_cetak_final($id){
		$query = "SELECT t_rpjmd_tujuan.*, m_skpd.nama_skpd, COUNT(t_rpjmd_tujuan.id) AS span FROM t_rpjmd_tujuan INNER JOIN t_rpjmd_sasaran ON t_rpjmd_tujuan.id=t_rpjmd_sasaran.id_tujuan INNER JOIN t_rpjmd_indikator ON t_rpjmd_sasaran.id=t_rpjmd_indikator.id_sasaran INNER JOIN t_rpjmd_program ON t_rpjmd_indikator.id=t_rpjmd_program.id_indikator AND t_rpjmd_sasaran.id=t_rpjmd_program.id_sasaran INNER JOIN t_renstra_prog_keg ON t_renstra_prog_keg.id=t_rpjmd_program.id_program_renstra AND t_renstra_prog_keg.is_prog_or_keg=1 INNER JOIN t_renstra ON t_renstra_prog_keg.id_renstra=t_renstra.id INNER JOIN m_skpd ON m_skpd.id_skpd=t_renstra.id_skpd WHERE id_misi=? GROUP BY t_rpjmd_tujuan.id";
		$result = $this->db->query($query, $id);
		return $result->result();		
	}

	function get_sasaran_rpjmd_4_cetak_final($id){
		$query = "SELECT t_rpjmd_sasaran.*, COUNT(t_rpjmd_sasaran.id) AS span FROM t_rpjmd_sasaran INNER JOIN t_rpjmd_indikator ON t_rpjmd_sasaran.id=t_rpjmd_indikator.id_sasaran INNER JOIN t_rpjmd_program ON t_rpjmd_indikator.id=t_rpjmd_program.id_indikator AND t_rpjmd_sasaran.id=t_rpjmd_program.id_sasaran WHERE id_tujuan=? GROUP BY t_rpjmd_sasaran.id";
		$result = $this->db->query($query, $id);
		return $result->result();		
	}

	function get_indikator_rpjmd_4_cetak_final($id){
		$query = "SELECT t_rpjmd_indikator.*, COUNT(t_rpjmd_indikator.id) AS span, m_lov.nama_value FROM t_rpjmd_indikator INNER JOIN t_rpjmd_sasaran ON t_rpjmd_indikator.id_sasaran=t_rpjmd_sasaran.id INNER JOIN t_rpjmd_program ON t_rpjmd_indikator.id=t_rpjmd_program.id_indikator AND t_rpjmd_sasaran.id=t_rpjmd_program.id_sasaran INNER JOIN m_lov ON t_rpjmd_indikator.satuan_target=m_lov.kode_value AND kode_app='1'WHERE t_rpjmd_indikator.id_sasaran=? GROUP BY t_rpjmd_indikator.id";
		$result = $this->db->query($query, $id);
		return $result->result();		
	}

	function get_program_rpjmd_4_cetak_final($id_sasaran, $id_indikator){
		$query = "SELECT t_rpjmd_program.id_sasaran, t_rpjmd_program.id_indikator, t_rpjmd_program.id_program_renstra, t_renstra_prog_keg.nama_prog_or_keg, m_bidang.Nm_Bidang AS nm_bidang, m_skpd.nama_skpd FROM t_rpjmd_program INNER JOIN t_renstra_prog_keg ON t_rpjmd_program.id_program_renstra=t_renstra_prog_keg.id INNER JOIN t_renstra ON t_renstra_prog_keg.id_renstra=t_renstra.id INNER JOIN m_skpd ON t_renstra.id_skpd=m_skpd.id_skpd INNER JOIN m_bidang ON (t_renstra_prog_keg.kd_urusan=m_bidang.Kd_Urusan AND t_renstra_prog_keg.kd_bidang=m_bidang.Kd_Bidang) WHERE t_rpjmd_program.id_sasaran=? AND t_rpjmd_program.id_indikator=?";
		$data = array($id_sasaran, $id_indikator);
		$result = $this->db->query($query, $data);				
		return $result->result();		
	}

	function get_revisi_pengajuan(){
		$query = "SELECT * FROM t_pengajuan_revisi INNER JOIN m_skpd ON t_pengajuan_revisi.id_skpd=m_skpd.id_skpd WHERE status=1";
		$result = $this->db->query($query);
		return $result->result();	
	}

	function get_revisi_skpd(){
		$query = "SELECT m_skpd.id_skpd AS id, m_skpd.nama_skpd, visi AS keterangan, SUM(IF(t_renstra_prog_keg.id_status=7, 1, 0)) AS total, COUNT(*) AS total1 FROM t_renstra INNER JOIN t_renstra_prog_keg ON t_renstra_prog_keg.id_renstra=t_renstra.id INNER JOIN m_skpd ON t_renstra.id_skpd=m_skpd.id_skpd GROUP BY t_renstra.id HAVING total=total1";
		$result = $this->db->query($query);
		return $result->result();		
	}

	function proses_revisi($skpd_setuju, $skpd_tdk_setuju, $with_pengajuan=TRUE){
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();
		
		if (!empty($skpd_setuju)) {
			$table_plus="";
			if ($with_pengajuan) {
				$this->db->where_in('id', $skpd_setuju);
				$this->db->update("t_pengajuan_revisi", array('status' => '3'));

				$table_plus=" INNER JOIN t_pengajuan_revisi ON t_renstra.id_skpd=t_pengajuan_revisi.id_skpd";
				$this->db->where_in('t_pengajuan_revisi.id', $skpd_setuju);
			}else{				
				$this->db->where_in('t_renstra.id_skpd', $skpd_setuju);
			}

			$this->db->update("t_renstra_prog_keg INNER JOIN t_renstra ON t_renstra_prog_keg.id_renstra=t_renstra.id".$table_plus, array('id_status' => '1'));
		}		

		if (!empty($skpd_tdk_setuju) && $with_pengajuan) {				
			$this->db->where_in('id', $skpd_tdk_setuju);
			$this->db->update("t_pengajuan_revisi", array('status' => '2'));
		}		

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	function simpan_revisi($id_skpd, $ket){
		$query = "INSERT INTO t_pengajuan_revisi(id_skpd, keterangan, status) VALUES (?, ?, 4)";
		$data = array($id_skpd, $ket);
		$result = $this->db->query($query, $data);

		$result = $this->proses_revisi($id_skpd, NULL, FALSE);
		return $result;
	}	
}
?>