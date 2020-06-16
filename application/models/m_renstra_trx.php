<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_renstra_trx extends CI_Model 
{
	var $table = 't_renstra';
	var $table_misi = 't_renstra_misi';
	var $table_tujuan = 't_renstra_tujuan';
	var $table_sasaran = 't_renstra_sasaran';
	var $table_program_kegiatan = 't_renstra_prog_keg';

	var $table_indikator_sasaran = 't_renstra_indikator_sasaran';
	var $table_indikator_program = 't_renstra_indikator_prog_keg';

	var $is_program = 1;
	var $is_kegiatan = 2;

	var $id_status_baru = "1";
	var $id_status_send = "2";
	var $id_status_revisi = "3";
	var $id_status_approved = "4";
	var $id_status_baru2 = "5";
	var $id_status_revisi2 = "6";
	var $id_status_approved2 = "7";		

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

	private function update_status_after_edit($id_renstra, $id_sasaran=NULL, $id_program=NULL, $id_kegiatan=NULL){		
		$proses = $this->cek_proses($id_renstra);
		
		if(!empty($id_sasaran)) {
			$this->db->where($this->table_program_kegiatan.".id_sasaran", $id_sasaran);
		}elseif (!empty($id_program)) {
			$this->db->where($this->table_program_kegiatan.".id", $id_program);
		}elseif (!empty($id_kegiatan)) {
			$this->db->where($this->table_program_kegiatan.".id", $id_kegiatan);
		}elseif (!empty($id_renstra)) {
			$this->db->where($this->table.".id", $id_renstra);
		}

		if (!empty($proses->proses2) && empty($proses->proses1)) {
			$this->db->where($this->table_program_kegiatan.".id_status <= ".$this->id_status_revisi2);
			$return = $this->db->update($this->table_program_kegiatan." INNER JOIN ". $this->table ." ON ". $this->table_program_kegiatan .".id_renstra=". $this->table .".id", array($this->table_program_kegiatan.'.id_status'=>$this->id_status_baru2));			
		}else{
			$this->db->where($this->table_program_kegiatan.".id_status <= ".$this->id_status_revisi);
			$return = $this->db->update($this->table_program_kegiatan." INNER JOIN ". $this->table ." ON ". $this->table_program_kegiatan .".id_renstra=". $this->table .".id", array($this->table_program_kegiatan.'.id_status'=>$this->id_status_baru));			
		}		
		return $return;
	}

	function cek_proses($id_renstra=NULL, $id_skpd=NULL){
		if (!empty($id_renstra) && !empty($id_skpd)) {
			$where = "t_renstra.id='". $id_renstra ."' AND t_renstra.id_skpd='". $id_skpd ."'";			
		}elseif (!empty($id_renstra)) {
			$where = "t_renstra.id='". $id_renstra ."'";
		}elseif (!empty($id_skpd)) {
			$where = "t_renstra.id_skpd='". $id_skpd ."'";
		}

		$query = "SELECT SUM(IF((t_renstra_prog_keg.id_status>=? AND t_renstra_prog_keg.id_status<?), 1, 0)) as proses1, SUM(IF((t_renstra_prog_keg.id_status>=? AND t_renstra_prog_keg.id_status<=?), 1, 0)) as proses2 FROM t_renstra_prog_keg INNER JOIN t_renstra ON t_renstra_prog_keg.id_renstra=t_renstra.id WHERE ".$where;
		$data = array($this->id_status_baru, $this->id_status_approved, $this->id_status_approved, $this->id_status_approved2);
		$result = $this->db->query($query, $data);
		return $result->row();
	}

	function get_one_renstra_skpd($id_skpd, $detail=FALSE){
		$this->db->select($this->table.".*");
		$this->db->from($this->table);
		$this->db->where($this->table.".id_skpd", $id_skpd);

		if ($detail) {
			$this->db->select("nama_skpd");
			$this->db->join("m_skpd","t_renstra.id_skpd = m_skpd.id_skpd","inner");
		}

		$result = $this->db->get();
		return $result->row();
	}

	function get_all_renstra_misi($id_renstra, $no_result=FALSE){
		$this->db->from($this->table_misi);		
		$this->db->where("id_renstra", $id_renstra);
		$result = $this->db->get();
		if ($no_result) {
			return $result;
		}else{
			return $result->result();
		}		
	}

	function get_all_renstra_tujuan($id_renstra, $no_result=FALSE){
		$this->db->from($this->table_tujuan);		
		$this->db->where("id_renstra", $id_renstra);
		$result = $this->db->get();
		if ($no_result) {
			return $result;
		}else{
			return $result->result();
		}	
	}

	function get_each_renstra_tujuan($id_renstra, $id_misi){
		$this->db->from($this->table_tujuan);		
		$this->db->where("id_renstra", $id_renstra);
		$this->db->where("id_misi", $id_misi);
		$result = $this->db->get();
		return $result;		
	}

	function get_one_renstra_tujuan($id_renstra, $id_tujuan){
		$this->db->from($this->table_tujuan);
		$this->db->where("id_renstra", $id_renstra);
		$this->db->where("id", $id_tujuan);
		$result = $this->db->get();
		return $result->row();
	}

	function add_renstra_skpd($data, $misi, $tujuan){
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();
		
		$data = $this->create_history($data);

		$this->db->insert($this->table, $data);
		$id_renstra = $this->db->insert_id();

		$id_misi = array();
		foreach ($misi as $key => $value) {			
			$id_misi[$key] = $this->add_misi(array('id_renstra' => $id_renstra, 'misi' => $value));
		}

		$tujuan_batch = array();
		foreach ($misi as $key => $value) {
			foreach ($tujuan[$key] as $key1 => $value1) {
				$tujuan_batch[] = array('id_renstra' => $id_renstra, 'id_misi' => $id_misi[$key], 'tujuan' => $value1);				
			}
		}		
		
		$this->add_tujuan($tujuan_batch);

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	function edit_renstra_skpd($data, $misi, $tujuan, $id_misi_old, $id_tujuan, $id_renstra){		
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();
		
		$data = $this->change_history($data);

		$this->db->where('id', $id_renstra);
		$this->db->update($this->table, $data);		
		
		$id_misi = array();		
		foreach ($misi as $key => $value) {
			if (!empty($id_misi_old[$key])) {
				$this->update_misi(array('id_renstra' => $id_renstra, 'misi' => $value), $id_misi_old[$key]);
				$id_misi[$key] = $id_misi_old[$key];
				unset($id_misi_old[$key]);
			}else{
				$id_misi[$key] = $this->add_misi(array('id_renstra' => $id_renstra, 'misi' => $value));
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
					$tujuan_batch[] = array('id_renstra' => $id_renstra, 'id_misi' => $id_misi[$key], 'tujuan' => $value1);
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

		$this->update_status_after_edit($id_renstra);

		$this->db->trans_complete();
		return $this->db->trans_status();
	}	

	function get_all_sasaran($id_renstra, $id_tujuan=NULL, $with_satuan=FALSE){
		$this->db->select($this->table_sasaran.".*");
		$this->db->where('id_renstra', $id_renstra);
		if (!empty($id_tujuan)) {
			$this->db->where('id_tujuan', $id_tujuan);
		}		
		$this->db->from($this->table_sasaran);

		if ($with_satuan) {
			$this->db->select("m_lov.nama_value");
			$this->db->join("m_lov",$this->table_sasaran.".satuan = m_lov.kode_value AND kode_app='1'","inner");
		}

		$result = $this->db->get();
		return $result->result();
	}

	function get_indikator_sasaran($id_sasaran, $return_result=TRUE){
		$this->db->where('id_sasaran', $id_sasaran);
		$this->db->from($this->table_indikator_sasaran);
		$result = $this->db->get();
		if ($return_result) {
			return $result->result();
		}else{
			return $result;
		}
	}

	function get_one_sasaran($id_renstra=NULL, $id_tujuan=NULL, $id_sasaran){
		if (!empty($id_renstra)) {
			$this->db->where('id_renstra', $id_renstra);
		}
		
		if (!empty($id_tujuan)) {
			$this->db->where('id_tujuan', $id_tujuan);
		}

		$this->db->where('id', $id_sasaran);
		$this->db->from($this->table_sasaran);	
		$result = $this->db->get();
		return $result->row();
	}

	function add_sasaran_skpd($data, $indikator){
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();

		$result = $this->db->insert($this->table_sasaran, $data);

		$id_sasaran = $this->db->insert_id();
		foreach ($indikator as $key => $value) {
			$this->db->insert($this->table_indikator_sasaran, array('id_sasaran' => $id_sasaran, 'indikator' => $value));
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	function edit_sasaran_skpd($data, $id_sasaran, $indikator, $id_indikator_sasaran){
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();

		$this->db->where('id', $id_sasaran);
		$result = $this->db->update($this->table_sasaran, $data);

		foreach ($indikator as $key => $value) {			
			if (!empty($id_indikator_sasaran[$key])) {
				$this->db->where('id', $id_indikator_sasaran[$key]);
				$this->db->where('id_sasaran', $id_sasaran);
				$this->db->update($this->table_indikator_sasaran, array('indikator' => $value));
				unset($id_indikator_sasaran[$key]);
			}else{
				$this->db->insert($this->table_indikator_sasaran, array('id_sasaran' => $id_sasaran, 'indikator' => $value));
			}
		}

		if (!empty($id_indikator_sasaran)) {
			$this->db->where_in('id', $id_indikator_sasaran);
			$this->db->delete($this->table_indikator_sasaran);			
		}

		$renstra = $this->get_one_sasaran(NULL, NULL, $id_sasaran);
		$this->update_status_after_edit($renstra->id_renstra, $id_sasaran);
		
		$this->db->trans_complete();
		return $this->db->trans_status();		
	}

	function delete_sasaran($id){
		$this->db->where('id', $id);		
		$result = $this->db->delete($this->table_sasaran); 
		return $result;
	}

	function get_all_program($id_renstra, $id_sasaran){
		$this->db->select($this->table_program_kegiatan.".*");
		$this->db->where('id_renstra', $id_renstra);
		$this->db->where('id_sasaran', $id_sasaran);
		$this->db->where('is_prog_or_keg', $this->is_program);
		$this->db->from($this->table_program_kegiatan);	
		$this->db->order_by('kd_urusan',"asc");
		$this->db->order_by('kd_bidang',"asc");
		$this->db->order_by('kd_program',"asc");	

		$result = $this->db->get();
		return $result->result();
	}

	function get_indikator_prog_keg($id, $return_result=TRUE, $satuan=FALSE){
		$this->db->select($this->table_indikator_program.".*");
		$this->db->where('id_prog_keg', $id);
		$this->db->from($this->table_indikator_program);

		if ($satuan) {			
			$this->db->select("m_lov.nama_value");
			$this->db->join("m_lov",$this->table_indikator_program.".satuan_target = m_lov.kode_value AND kode_app='1'","inner");		
		}

		$result = $this->db->get();
		if ($return_result) {
			return $result->result();
		}else{
			return $result;
		}
	}

	function get_one_program($id_renstra=NULL, $id_sasaran=NULL, $id_program, $detail=FALSE){
		if (!empty($id_renstra)) {
			$this->db->where($this->table_program_kegiatan.'.id_renstra', $id_renstra);
		}
		
		if (!empty($id_sasaran)) {
			$this->db->where($this->table_program_kegiatan.'.id_sasaran', $id_sasaran);
		}

		if ($detail) {
			$this->db->select($this->table_program_kegiatan.".*");
			$this->db->select("nama_skpd");
			$this->db->select("tujuan");
			$this->db->select("sasaran");			

			$this->db->join($this->table, $this->table_program_kegiatan.".id_renstra = ".$this->table.".id","inner");
			$this->db->join("m_skpd", $this->table.".id_skpd = m_skpd.id_skpd","inner");
			$this->db->join($this->table_sasaran, $this->table_program_kegiatan.".id_sasaran = ".$this->table_sasaran.".id","inner");
			$this->db->join($this->table_tujuan, $this->table_sasaran.".id_tujuan = ".$this->table_tujuan.".id","inner");						

			$this->db->select("m_urusan.Nm_Urusan");
			$this->db->select("m_bidang.Nm_Bidang");
			$this->db->select("m_program.Ket_Program");
			$this->db->join("m_urusan",$this->table_program_kegiatan.".kd_urusan = m_urusan.Kd_Urusan","inner");
			$this->db->join("m_bidang",$this->table_program_kegiatan.".kd_urusan = m_bidang.Kd_Urusan AND ".$this->table_program_kegiatan.".kd_bidang = m_bidang.Kd_Bidang","inner");
			$this->db->join("m_program",$this->table_program_kegiatan.".kd_urusan = m_program.Kd_Urusan AND ".$this->table_program_kegiatan.".kd_bidang = m_program.Kd_Bidang AND ".$this->table_program_kegiatan.".kd_program = m_program.Kd_Prog","inner");
		}

		$this->db->where($this->table_program_kegiatan.'.id', $id_program);
		$this->db->from($this->table_program_kegiatan);	
		$result = $this->db->get();
		return $result->row();
	}

	function get_info_tujuan_n_sasaran_n_program($id_tujuan=NULL, $id_sasaran=NULL, $id_program=NULL){
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
		if (!empty($id_program)) {
			$this->db->select($this->table_program_kegiatan.".kd_urusan");
			$this->db->select($this->table_program_kegiatan.".kd_bidang");
			$this->db->select($this->table_program_kegiatan.".kd_program");
			$this->db->select($this->table_program_kegiatan.".nama_prog_or_keg");
			$this->db->join($this->table_program_kegiatan,$this->table_program_kegiatan.".id_sasaran = ". $this->table_sasaran .".id","inner");			
			$this->db->where($this->table_program_kegiatan.'.id', $id_program);
		}

		$result = $this->db->get();
		return $result->row();
	}

	function add_program_skpd($data, $indikator, $satuan_target, $kondisi_awal, $target_1, $target_2, $target_3, $target_4, $target_5, $target_kondisi_akhir){
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();

		$add = array('is_prog_or_keg'=> $this->is_program);
		$data = $this->global_function->add_array($data, $add);		

		$this->db->insert($this->table_program_kegiatan, $data);

		$id = $this->db->insert_id();
		foreach ($indikator as $key => $value) {
			$this->db->insert($this->table_indikator_program, array('id_prog_keg' => $id, 'indikator' => $value, 'satuan_target' => $satuan_target[$key], 'kondisi_awal' => $kondisi_awal[$key], 'target_1' => $target_1[$key], 'target_2' => $target_2[$key], 'target_3' => $target_3[$key], 'target_4' => $target_4[$key], 'target_5' => $target_5[$key], 'target_kondisi_akhir' => $target_kondisi_akhir[$key]));
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	function edit_program_skpd($data, $id_program, $indikator, $id_indikator_program, $satuan_target, $kondisi_awal, $target_1, $target_2, $target_3, $target_4, $target_5, $target_kondisi_akhir){
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();

		$add = array('is_prog_or_keg'=> $this->is_program);
		$data = $this->global_function->add_array($data, $add);		

		$this->db->where('id', $id_program);
		$result = $this->db->update($this->table_program_kegiatan, $data);

		foreach ($indikator as $key => $value) {			
			if (!empty($id_indikator_program[$key])) {
				$this->db->where('id', $id_indikator_program[$key]);
				$this->db->where('id_prog_keg', $id_program);
				$this->db->update($this->table_indikator_program, array('indikator' => $value, 'satuan_target' => $satuan_target[$key], 'kondisi_awal' => $kondisi_awal[$key], 'target_1' => $target_1[$key], 'target_2' => $target_2[$key], 'target_3' => $target_3[$key], 'target_4' => $target_4[$key], 'target_5' => $target_5[$key], 'target_kondisi_akhir' => $target_kondisi_akhir[$key]));
				unset($id_indikator_program[$key]);
			}else{
				$this->db->insert($this->table_indikator_program, array('id_prog_keg' => $id_program, 'indikator' => $value, 'satuan_target' => $satuan_target[$key], 'kondisi_awal' => $kondisi_awal[$key], 'target_1' => $target_1[$key], 'target_2' => $target_2[$key], 'target_3' => $target_3[$key], 'target_4' => $target_4[$key], 'target_5' => $target_5[$key], 'target_kondisi_akhir' => $target_kondisi_akhir[$key]));
			}
		}

		if (!empty($id_indikator_program)) {
			$this->db->where_in('id', $id_indikator_program);
			$this->db->delete($this->table_indikator_program);			
		}

		$renstra = $this->get_one_program(NULL, NULL, $id_program);
		$this->update_status_after_edit($renstra->id_renstra, NULL, $id_program);

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	function delete_program($id){
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();

		$this->db->where('id', $id);
		$this->db->where('is_prog_or_keg', $this->is_program);
		$this->db->delete($this->table_program_kegiatan); 

		$this->db->where('parent', $id);
		$this->db->where('is_prog_or_keg', $this->is_kegiatan);
		$this->db->delete($this->table_program_kegiatan); 
		
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	function get_all_kegiatan($id_renstra, $id_sasaran, $id_program, $with_satuan){
		$this->db->select($this->table_program_kegiatan.".*");
		$this->db->where('id_renstra', $id_renstra);
		$this->db->where('id_sasaran', $id_sasaran);
		$this->db->where('parent', $id_program);
		$this->db->where('is_prog_or_keg', $this->is_kegiatan);
		$this->db->from($this->table_program_kegiatan);
		$this->db->order_by('kd_urusan',"asc");
		$this->db->order_by('kd_bidang',"asc");
		$this->db->order_by('kd_program',"asc");
		$this->db->order_by('kd_kegiatan',"asc");

		if ($with_satuan) {
			$this->db->select("m_lov.nama_value");
			$this->db->join("m_lov",$this->table_program_kegiatan.".satuan_target = m_lov.kode_value AND kode_app='1'","inner");
		}

		$result = $this->db->get();
		return $result->result();
	}
	
	function add_kegiatan_skpd($data, $indikator, $satuan_target, $kondisi_awal, $target_1, $target_2, $target_3, $target_4, $target_5, $target_kondisi_akhir){
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();

		$add = array('is_prog_or_keg'=> $this->is_kegiatan, 'id_status'=> $this->id_status_baru);
		$data = $this->global_function->add_array($data, $add);		

		$this->db->insert($this->table_program_kegiatan, $data);

		$id = $this->db->insert_id();
		foreach ($indikator as $key => $value) {
			$this->db->insert($this->table_indikator_program, array('id_prog_keg' => $id, 'indikator' => $value, 'satuan_target' => $satuan_target[$key], 'kondisi_awal' => $kondisi_awal[$key], 'target_1' => $target_1[$key], 'target_2' => $target_2[$key], 'target_3' => $target_3[$key], 'target_4' => $target_4[$key], 'target_5' => $target_5[$key], 'target_kondisi_akhir' => $target_kondisi_akhir[$key]));
		}

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	function edit_kegiatan_skpd($data, $id_kegiatan, $indikator, $id_indikator_kegiatan, $satuan_target, $kondisi_awal, $target_1, $target_2, $target_3, $target_4, $target_5, $target_kondisi_akhir){
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();

		$add = array('is_prog_or_keg'=> $this->is_kegiatan);
		$data = $this->global_function->add_array($data, $add);		

		$this->db->where('id', $id_kegiatan);
		$result = $this->db->update($this->table_program_kegiatan, $data);

		foreach ($indikator as $key => $value) {			
			if (!empty($id_indikator_kegiatan[$key])) {
				$this->db->where('id', $id_indikator_kegiatan[$key]);
				$this->db->where('id_prog_keg', $id_kegiatan);
				$this->db->update($this->table_indikator_program, array('indikator' => $value, 'satuan_target' => $satuan_target[$key], 'kondisi_awal' => $kondisi_awal[$key], 'target_1' => $target_1[$key], 'target_2' => $target_2[$key], 'target_3' => $target_3[$key], 'target_4' => $target_4[$key], 'target_5' => $target_5[$key], 'target_kondisi_akhir' => $target_kondisi_akhir[$key]));
				unset($id_indikator_kegiatan[$key]);
			}else{
				$this->db->insert($this->table_indikator_program, array('id_prog_keg' => $id_kegiatan, 'indikator' => $value, 'satuan_target' => $satuan_target[$key], 'kondisi_awal' => $kondisi_awal[$key], 'target_1' => $target_1[$key], 'target_2' => $target_2[$key], 'target_3' => $target_3[$key], 'target_4' => $target_4[$key], 'target_5' => $target_5[$key], 'target_kondisi_akhir' => $target_kondisi_akhir[$key]));
			}
		}

		if (!empty($id_indikator_kegiatan)) {
			$this->db->where_in('id', $id_indikator_kegiatan);
			$this->db->delete($this->table_indikator_program);			
		}

		$renstra = $this->get_one_kegiatan(NULL, NULL, NULL, $id_kegiatan);
		$this->update_status_after_edit($renstra->id_renstra, NULL, NULL, $id_kegiatan);

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	function delete_kegiatan($id){
		$this->db->where('id', $id);
		$this->db->where('is_prog_or_keg', $this->is_kegiatan);
		$result = $this->db->delete($this->table_program_kegiatan); 
		return $result;
	}

	function get_one_kegiatan($id_renstra=NULL, $id_sasaran=NULL, $id_program=NULL, $id_kegiatan, $detail=FALSE){		
		if (!empty($id_renstra)) {
			$this->db->where($this->table_program_kegiatan.'.id_renstra', $id_renstra);
		}
		
		if (!empty($id_sasaran)) {
			$this->db->where($this->table_program_kegiatan.'.id_sasaran', $id_sasaran);
		}

		if (!empty($id_program)) {
			$this->db->where('parent', $id_program);
		}

		if ($detail) {
			$this->db->select($this->table_program_kegiatan.".*");
			$this->db->select("nama_skpd");
			$this->db->select("tujuan");
			$this->db->select("sasaran");			

			$this->db->join($this->table, $this->table_program_kegiatan.".id_renstra = ".$this->table.".id","inner");
			$this->db->join("m_skpd", $this->table.".id_skpd = m_skpd.id_skpd","inner");
			$this->db->join($this->table_sasaran, $this->table_program_kegiatan.".id_sasaran = ".$this->table_sasaran.".id","inner");
			$this->db->join($this->table_tujuan, $this->table_sasaran.".id_tujuan = ".$this->table_tujuan.".id","inner");						

			$this->db->select("m_urusan.Nm_Urusan");
			$this->db->select("m_bidang.Nm_Bidang");
			$this->db->select("m_program.Ket_Program");
			$this->db->join("m_urusan",$this->table_program_kegiatan.".kd_urusan = m_urusan.Kd_Urusan","inner");
			$this->db->join("m_bidang",$this->table_program_kegiatan.".kd_urusan = m_bidang.Kd_Urusan AND ".$this->table_program_kegiatan.".kd_bidang = m_bidang.Kd_Bidang","inner");
			$this->db->join("m_program",$this->table_program_kegiatan.".kd_urusan = m_program.Kd_Urusan AND ".$this->table_program_kegiatan.".kd_bidang = m_program.Kd_Bidang AND ".$this->table_program_kegiatan.".kd_program = m_program.Kd_Prog","inner");
		}

		$this->db->where($this->table_program_kegiatan.'.id', $id_kegiatan);
		$this->db->from($this->table_program_kegiatan);	
		$result = $this->db->get();
		return $result->row();
	}
	
	function kirim_renstra($id_skpd){
		$proses = $this->cek_proses(NULL, $id_skpd);

		if (!empty($proses->proses2) && empty($proses->proses1)) {
			$id_status_data = $this->id_status_approved;
		}else{			
			$id_status_data = $this->id_status_send;
		}		
		

		$this->db->where($this->table.".id_skpd", $id_skpd);
		$this->db->where($this->table_program_kegiatan.".id_status !=", $this->id_status_approved);
		$return = $this->db->update($this->table_program_kegiatan." INNER JOIN ". $this->table ." ON ". $this->table_program_kegiatan .".id_renstra=". $this->table .".id", array($this->table_program_kegiatan.'.id_status'=>$id_status_data));
		return $return;
	}

	function count_jendela_kontrol($id_skpd){
		$skpd_search="";
		if ($id_skpd!='all') {
			$skpd_search="WHERE t_renstra.id_skpd=?";
		}			
		$query = "SELECT SUM(IF(t_renstra_prog_keg.id_status=?, 1, 0)) as baru, SUM(IF(t_renstra_prog_keg.id_status>=?, 1, 0)) as kirim, SUM(IF(t_renstra_prog_keg.id_status>?, 1, 0)) as proses, SUM(IF((t_renstra_prog_keg.id_status=? OR t_renstra_prog_keg.id_status=?), 1, 0)) as revisi, SUM(IF(t_renstra_prog_keg.id_status=? OR t_renstra_prog_keg.id_status=?, 1, 0)) as veri, SUM(IF((t_renstra_prog_keg.id_status>=? AND t_renstra_prog_keg.id_status<=?), 1, 0)) as proses1, SUM(IF((t_renstra_prog_keg.id_status>=? AND t_renstra_prog_keg.id_status<=?), 1, 0)) as proses2, SUM(IF(t_renstra_prog_keg.id_status=?, 1, 0)) as baru2 FROM t_renstra_prog_keg INNER JOIN t_renstra ON t_renstra_prog_keg.id_renstra=t_renstra.id WHERE t_renstra.id_skpd=? AND is_prog_or_keg=?";
		
		$query = "SELECT 
						SUM(IF(t_renstra_prog_keg.id_status=?, 1, 0)) as baru, 
						SUM(IF(t_renstra_prog_keg.id_status>=?, 1, 0)) as kirim, 
						SUM(IF(t_renstra_prog_keg.id_status>?, 1, 0)) as proses, 
						SUM(IF(t_renstra_prog_keg.id_status=?, 1, 0)) as revisi, 						
						SUM(IF(t_renstra_prog_keg.id_status>=?, 1, 0)) as veri, 
						SUM(IF(t_renstra_prog_keg.id_status=?, 1, 0)) as baru2, 
						SUM(IF(t_renstra_prog_keg.id_status>=?, 1, 0)) as kirim2, 
						SUM(IF(t_renstra_prog_keg.id_status>?, 1, 0)) as proses2,
						SUM(IF(t_renstra_prog_keg.id_status=?, 1, 0)) as revisi2, 
						SUM(IF(t_renstra_prog_keg.id_status>=?, 1, 0)) as veri2						
					FROM 
						t_renstra_prog_keg 
					INNER JOIN 
						t_renstra ON t_renstra_prog_keg.id_renstra=t_renstra.id 
					".$skpd_search;		
		$data = array(
					$this->id_status_baru, 
					$this->id_status_send, 
					$this->id_status_send, 
					$this->id_status_revisi, 
					$this->id_status_approved, 
					$this->id_status_baru2,
					$this->id_status_approved,
					$this->id_status_baru2,
					$this->id_status_revisi2, 
					$this->id_status_approved2,					
					$id_skpd);
		$result = $this->db->query($query, $data);
		return $result->row();
	}

	function get_all_renstra($search, $start, $length, $order, $id_skpd, $order_arr, $status = NULL, $detail = FALSE){		
		$this->db->select($this->table_program_kegiatan.".*");
		$this->db->select("status_renstra");
		$this->db->from($this->table_program_kegiatan);
		$this->db->join($this->table,$this->table.".id = ". $this->table_program_kegiatan .".id_renstra","inner");
		$this->db->where($this->table_program_kegiatan.".is_prog_or_keg", $this->is_kegiatan);
				
		if ($id_skpd != "all") {			
			$this->db->where($this->table.".id_skpd", $id_skpd);
		}
		if ($status=="BARU") {
			$this->db->where("id_status", $this->id_status_baru);
		}elseif ($status=="VERIFIKASI") {
			$this->db->where("id_status", $this->id_status_send);
		}elseif ($status=="APPROVED") {
			$this->db->where("id_status", $this->id_status_approved);
		}
		if (!is_null($search)) {
			$this->db->where("(CONCAT(kd_urusan,\".\",kd_bidang,\".\",kd_program,\".\",kd_kegiatan) LIKE '%". $search['value'] ."%' OR nama_prog_or_keg LIKE '%". $search['value'] ."%' OR indikator_kinerja LIKE '%". $search['value'] ."%' OR status_renstra LIKE '%". $search['value'] ."%')");
		}

		if ($detail) {			
			$this->db->select("m_bidkoordinasi.nama_koor");
			$this->db->select("m_skpd.nama_skpd");
			$this->db->join("m_skpd",$this->table.".id_skpd = m_skpd.id_skpd","inner");
			$this->db->join("m_bidkoordinasi","m_skpd.id_bidkoor = m_bidkoordinasi.id_bidkoor","inner");
		}
		
		if (!is_null($length) && !is_null($start)) {
			$this->db->limit($length, $start);
		}
		if (!is_null($order)) {
			$this->db->order_by($order_arr[$order["column"]], $order["dir"]); 
		}		

		$this->db->join("m_status_renstra",$this->table_program_kegiatan.".id_status = m_status_renstra.id","inner");
		$result = $this->db->get();
		return $result->result();
	}

	function count_all_renstra($search, $id_skpd, $status = NULL){		
		$this->db->from($this->table_program_kegiatan);
		$this->db->join($this->table,$this->table.".id = ". $this->table_program_kegiatan .".id_renstra","inner");
		$this->db->where($this->table_program_kegiatan.".is_prog_or_keg", $this->is_kegiatan);

		if ($id_skpd != "all") {			
			$this->db->where($this->table.".id_skpd", $id_skpd);
		}
		if ($status=="BARU") {
			$this->db->where("id_status", $this->id_status_baru);
		}elseif ($status=="VERIFIKASI") {
			$this->db->where("id_status", $this->id_status_send);
		}elseif ($status=="APPROVED") {
			$this->db->where("id_status", $this->id_status_approved);
		}
		if (!is_null($search)) {
			$this->db->where("(CONCAT(kd_urusan,\".\",kd_bidang,\".\",kd_program,\".\",kd_kegiatan) LIKE '%". $search['value'] ."%' OR nama_prog_or_keg LIKE '%". $search['value'] ."%' OR indikator_kinerja LIKE '%". $search['value'] ."%' OR status_renstra LIKE '%". $search['value'] ."%')");
		}

		$this->db->join("m_status_renstra",$this->table_program_kegiatan.".id_status = m_status_renstra.id","inner");
		$result = $this->db->count_all_results();
		return $result;
	}

	function get_all_renstra_veri(){		
		$query = "SELECT t_renstra.*, m_skpd.*, COUNT(t_renstra.id) AS jum_semua,SUM(IF(t_renstra_prog_keg.id_status=?,1,0)) AS jum_dikirim FROM t_renstra INNER JOIN t_renstra_prog_keg ON t_renstra.id=t_renstra_prog_keg.id_renstra INNER JOIN m_skpd ON t_renstra.id_skpd=m_skpd.id_skpd GROUP BY m_skpd.id_skpd HAVING jum_dikirim>0";
		$data = array($this->id_status_send);
		$result = $this->db->query($query, $data);
		return $result->result();
	}

	function get_program_skpd_4_cetak($id_skpd){
		$query = "SELECT 
						tujuan, t_renstra_sasaran.sasaran, t_renstra_sasaran.id AS id_sasaran, pro.*, SUM(keg.nominal_1) AS nominal_1_pro, SUM(keg.nominal_2) AS nominal_2_pro, SUM(keg.nominal_3) AS nominal_3_pro, SUM(keg.nominal_4) AS nominal_4_pro, SUM(keg.nominal_5) AS nominal_5_pro
					FROM 
						(SELECT * FROM t_renstra_prog_keg WHERE is_prog_or_keg=1) AS pro 
					INNER JOIN 
						(SELECT * FROM t_renstra_prog_keg WHERE is_prog_or_keg=2) AS keg ON keg.parent=pro.id 
					INNER JOIN 
						t_renstra_sasaran ON t_renstra_sasaran.id=pro.id_sasaran 
					INNER JOIN 
						t_renstra_tujuan ON t_renstra_tujuan.id=t_renstra_sasaran.id_tujuan
					INNER JOIN 
						t_renstra ON pro.id_renstra=t_renstra.id					
					WHERE 
						t_renstra.id_skpd=?
					GROUP BY pro.id
					ORDER BY t_renstra_tujuan.id ASC, kd_urusan ASC, kd_bidang ASC, kd_program ASC";
		$data = array($id_skpd);
		$result = $this->db->query($query, $data);
		return $result->result();	
	}

	function get_kegiatan_skpd_4_cetak($id_program){
		$query = "SELECT 
						t_renstra_prog_keg.*
					FROM 
						t_renstra_prog_keg 					
					WHERE 
						parent=?
					ORDER BY kd_urusan ASC, kd_kegiatan ASC, kd_program ASC, kd_kegiatan ASC";
		$data = array($id_program);
		$result = $this->db->query($query, $data);
		return $result;		
	}

	function get_total_kegiatan_dan_indikator($id_program){
		$query = "SELECT 
						COUNT(*) AS total
					FROM 
						t_renstra_prog_keg 	
					INNER JOIN 
						t_renstra_indikator_prog_keg ON t_renstra_indikator_prog_keg.id_prog_keg=t_renstra_prog_keg.id
					WHERE 
						parent=? OR t_renstra_prog_keg.id=?";
		$data = array($id_program, $id_program);
		$result = $this->db->query($query, $data);
		return $result->row();		
	}

	function approved_renstra($id_renstra, $id){
		$this->db->where($this->table_program_kegiatan.".id", $id);
		$this->db->where($this->table_program_kegiatan.".id_renstra", $id_renstra);
		$return = $this->db->update($this->table_program_kegiatan, array('id_status'=>$this->id_status_approved));
		return $return;		
	}

	function not_approved_renstra($id_renstra, $id, $ket){
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();

		$this->db->where($this->table_program_kegiatan.".id", $id);
		$this->db->where($this->table_program_kegiatan.".id_renstra", $id_renstra);
		$return = $this->db->update($this->table_program_kegiatan, array('id_status'=>$this->id_status_revisi));

		$result = $this->db->insert("t_renstra_revisi", array('id_renstra' => $id_renstra, 'id_kegiatan' => $id, 'ket' => $ket));
		
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	function get_revisi_renstra_ranwal($id_renstra){
		$this->db->select("t_renstra_revisi.ket");
		$this->db->select("t_renstra_prog_keg.*");
		$this->db->select("t_renstra_sasaran.sasaran");
		$this->db->select("t_renstra_tujuan.tujuan");
		$this->db->from("t_renstra_prog_keg");
		
		$this->db->where("t_renstra_revisi.id_renstra", $id_renstra);
		$this->db->where("t_renstra_prog_keg.id_status", $this->id_status_revisi);

		$this->db->join("(SELECT * FROM (SELECT * FROM t_renstra_revisi ORDER BY id DESC) AS t_renstra_revisi GROUP BY id_renstra,id_kegiatan) AS t_renstra_revisi","t_renstra_revisi.id_kegiatan=t_renstra_prog_keg.id","inner");
		$this->db->join("t_renstra_sasaran","t_renstra_sasaran.id=t_renstra_prog_keg.id_sasaran","inner");
		$this->db->join("t_renstra_tujuan","t_renstra_tujuan.id=t_renstra_sasaran.id_tujuan","inner");
		$this->db->order_by("t_renstra_revisi.id", "desc");		
		$result = $this->db->get();
		return $result->result();
	}

	function get_revisi_renstra_akhir($id_renstra){
		$query = "SELECT `t_renstra_revisi_keg`.*, `t_renstra_prog_keg`.`kd_urusan`, `t_renstra_prog_keg`.`kd_bidang`, `t_renstra_prog_keg`.`kd_program`, `t_renstra_prog_keg`.`nama_prog_or_keg` FROM (SELECT * FROM `t_renstra_revisi_keg` WHERE id IN (SELECT max(id) FROM t_renstra_revisi_keg GROUP BY id_prog_keg) GROUP BY id_prog_keg) AS `t_renstra_revisi_keg` INNER JOIN `t_renstra_prog_keg` ON `t_renstra_revisi_keg`.`id_prog_keg` = `t_renstra_prog_keg`.`id` WHERE `id_renstra` = ? AND is_revisi_rpjm='0' ORDER BY `t_renstra_revisi_keg`.`id` desc";
		$data = array($id_renstra);
		$result = $this->db->query($query, $data);
		return $result->result();
	}

	function get_revisi_rpjm($id_renstra){
		$query = "SELECT `t_renstra_revisi_keg`.*, `t_renstra_prog_keg`.`kd_urusan`, `t_renstra_prog_keg`.`kd_bidang`, `t_renstra_prog_keg`.`kd_program`, `t_renstra_prog_keg`.`nama_prog_or_keg`, `t_renstra_prog_keg`.`nominal_1_tot` AS nominal_1_sblm, `t_renstra_prog_keg`.`nominal_2_tot` AS nominal_2_sblm, `t_renstra_prog_keg`.`nominal_3_tot` AS nominal_3_sblm, `t_renstra_prog_keg`.`nominal_4_tot` AS nominal_4_sblm, `t_renstra_prog_keg`.`nominal_5_tot` AS nominal_5_sblm FROM (SELECT * FROM `t_renstra_revisi_keg` WHERE id IN (SELECT max(id) FROM t_renstra_revisi_keg GROUP BY id_prog_keg) GROUP BY id_prog_keg) AS `t_renstra_revisi_keg` INNER JOIN (SELECT vw.*, SUM(t_renstra_prog_keg.nominal_1) AS nominal_1_tot, SUM(t_renstra_prog_keg.nominal_2) AS nominal_2_tot, SUM(t_renstra_prog_keg.nominal_3) AS nominal_3_tot, SUM(t_renstra_prog_keg.nominal_4) AS nominal_4_tot, SUM(t_renstra_prog_keg.nominal_5) AS nominal_5_tot FROM (SELECT * FROM t_renstra_prog_keg WHERE is_prog_or_keg=?) AS vw INNER JOIN t_renstra_prog_keg ON vw.id=t_renstra_prog_keg.parent AND t_renstra_prog_keg.is_prog_or_keg=? GROUP BY vw.id) AS `t_renstra_prog_keg` ON `t_renstra_revisi_keg`.`id_prog_keg` = `t_renstra_prog_keg`.`id` WHERE `id_renstra` = ? AND is_revisi_rpjm='1' ORDER BY `t_renstra_revisi_keg`.`id` desc";
		$data = array($this->is_program, $this->is_kegiatan, $id_renstra);
		$result = $this->db->query($query, $data);
		return $result->result();
	}

	function get_all_renstra_veri_akhir(){
		$query = "SELECT COUNT(*) AS jml_data, m_urusan.*, m_bidang.* FROM t_renstra_prog_keg INNER JOIN m_bidang ON (t_renstra_prog_keg.kd_urusan=m_bidang.Kd_Urusan AND t_renstra_prog_keg.kd_bidang=m_bidang.Kd_Bidang) INNER JOIN m_urusan ON t_renstra_prog_keg.kd_urusan=m_urusan.Kd_Urusan WHERE t_renstra_prog_keg.id_status=? AND t_renstra_prog_keg.is_prog_or_keg=? GROUP BY t_renstra_prog_keg.kd_urusan, t_renstra_prog_keg.kd_bidang";
		$data = array($this->id_status_approved, $this->is_kegiatan);
		$result = $this->db->query($query, $data);
		return $result->result();
	}

	function get_bidang_urusan_4_cetak_final($urusan, $bidang){
		$query = "SELECT t_renstra_prog_keg.*, m_bidang.*,SUM(t_renstra_prog_keg.nominal_1) AS nominal_1_pro, SUM(t_renstra_prog_keg.nominal_2) AS nominal_2_pro, SUM(t_renstra_prog_keg.nominal_3) AS nominal_3_pro, SUM(t_renstra_prog_keg.nominal_4) AS nominal_4_pro, SUM(t_renstra_prog_keg.nominal_5) AS nominal_5_pro FROM t_renstra_prog_keg INNER JOIN m_bidang ON (t_renstra_prog_keg.kd_urusan=m_bidang.Kd_Urusan AND t_renstra_prog_keg.kd_bidang=m_bidang.Kd_Bidang) WHERE t_renstra_prog_keg.id_status=? AND t_renstra_prog_keg.is_prog_or_keg=? AND t_renstra_prog_keg.kd_urusan=? AND t_renstra_prog_keg.kd_bidang=? GROUP BY t_renstra_prog_keg.kd_urusan, t_renstra_prog_keg.kd_bidang";
		$data = array($this->id_status_approved, $this->is_kegiatan, $urusan, $bidang);
		$result = $this->db->query($query, $data);
		return $result->result();	
	}

	function get_bidang_urusan_skpd_4_cetak_final($urusan, $bidang){
		$query = "SELECT t_renstra_prog_keg.*,m_skpd.*,SUM(t_renstra_prog_keg.nominal_1) AS nominal_1_pro, SUM(t_renstra_prog_keg.nominal_2) AS nominal_2_pro, SUM(t_renstra_prog_keg.nominal_3) AS nominal_3_pro, SUM(t_renstra_prog_keg.nominal_4) AS nominal_4_pro, SUM(t_renstra_prog_keg.nominal_5) AS nominal_5_pro FROM t_renstra_prog_keg INNER JOIN t_renstra ON (t_renstra.id=t_renstra_prog_keg.id_renstra AND t_renstra_prog_keg.is_prog_or_keg=?) INNER JOIN m_skpd ON t_renstra.id_skpd=m_skpd.id_skpd WHERE t_renstra_prog_keg.id_status=? AND t_renstra_prog_keg.is_prog_or_keg=? AND t_renstra_prog_keg.kd_urusan=? AND t_renstra_prog_keg.kd_bidang=? GROUP BY t_renstra_prog_keg.kd_urusan, t_renstra_prog_keg.kd_bidang, m_skpd.id_skpd";
		$data = array($this->is_kegiatan, $this->id_status_approved, $this->is_kegiatan, $urusan, $bidang);
		$result = $this->db->query($query, $data);
		return $result->result();	
	}

	function get_bidang_urusan_skpd_program_4_cetak_final($urusan, $bidang, $skpd){
		$query = "SELECT vw1.*,m_skpd.*,SUM(t_renstra_prog_keg.nominal_1) AS nominal_1_pro, SUM(t_renstra_prog_keg.nominal_2) AS nominal_2_pro, SUM(t_renstra_prog_keg.nominal_3) AS nominal_3_pro, SUM(t_renstra_prog_keg.nominal_4) AS nominal_4_pro, SUM(t_renstra_prog_keg.nominal_5) AS nominal_5_pro FROM t_renstra_prog_keg INNER JOIN t_renstra ON (t_renstra.id=t_renstra_prog_keg.id_renstra AND t_renstra_prog_keg.is_prog_or_keg=?) INNER JOIN m_skpd ON t_renstra.id_skpd=m_skpd.id_skpd INNER JOIN t_renstra_prog_keg AS vw1 ON (vw1.id=t_renstra_prog_keg.parent AND vw1.is_prog_or_keg=?) WHERE t_renstra_prog_keg.id_status=? AND t_renstra_prog_keg.is_prog_or_keg=? AND t_renstra_prog_keg.kd_urusan=? AND t_renstra_prog_keg.kd_bidang=? AND t_renstra.id_skpd=? GROUP BY t_renstra_prog_keg.kd_urusan, t_renstra_prog_keg.kd_bidang, m_skpd.id_skpd, t_renstra_prog_keg.kd_program";				
		$data = array($this->is_kegiatan, $this->is_program, $this->id_status_approved, $this->is_kegiatan, $urusan, $bidang, $skpd);
		$result = $this->db->query($query, $data);		
		return $result->result();	
	}

	function get_one_bidang_urusan_skpd_program_final($id_program){
		$query = "SELECT vw1.*,m_bidang.Nm_Bidang,m_skpd.*,SUM(t_renstra_prog_keg.nominal_1) AS nominal_1_pro, SUM(t_renstra_prog_keg.nominal_2) AS nominal_2_pro, SUM(t_renstra_prog_keg.nominal_3) AS nominal_3_pro, SUM(t_renstra_prog_keg.nominal_4) AS nominal_4_pro, SUM(t_renstra_prog_keg.nominal_5) AS nominal_5_pro FROM t_renstra_prog_keg INNER JOIN t_renstra ON (t_renstra.id=t_renstra_prog_keg.id_renstra AND t_renstra_prog_keg.is_prog_or_keg=?) INNER JOIN m_skpd ON t_renstra.id_skpd=m_skpd.id_skpd INNER JOIN t_renstra_prog_keg AS vw1 ON (vw1.id=t_renstra_prog_keg.parent AND vw1.is_prog_or_keg=?) INNER JOIN m_bidang ON m_bidang.Kd_Urusan=vw1.kd_urusan AND m_bidang.Kd_Bidang=vw1.kd_bidang WHERE t_renstra_prog_keg.id_status=? AND t_renstra_prog_keg.is_prog_or_keg=? AND vw1.id=? GROUP BY t_renstra_prog_keg.kd_urusan, t_renstra_prog_keg.kd_bidang, m_skpd.id_skpd, t_renstra_prog_keg.kd_program";				
		$data = array($this->is_kegiatan, $this->is_program, $this->id_status_approved, $this->is_kegiatan, $id_program);
		$result = $this->db->query($query, $data);		
		return $result->row();	
	}

	function get_program_veri_akhir($urusan, $bidang){
		$query = "SELECT * FROM t_renstra_prog_keg INNER JOIN m_bidang ON (t_renstra_prog_keg.kd_urusan=m_bidang.Kd_Urusan AND t_renstra_prog_keg.kd_bidang=m_bidang.Kd_Bidang) INNER JOIN m_urusan ON t_renstra_prog_keg.kd_urusan=m_urusan.Kd_Urusan WHERE t_renstra_prog_keg.id_status=? AND t_renstra_prog_keg.is_prog_or_keg=? GROUP BY t_renstra_prog_keg.kd_urusan, t_renstra_prog_keg.kd_bidang";
		$data = array($this->id_status_approved, $this->is_kegiatan);
		$result = $this->db->query($query, $data);
		return $result->result();	
	}

	function approved_veri_akhir_renstra($id){
		$this->db->where($this->table_program_kegiatan.".parent", $id);				
		$this->db->or_where($this->table_program_kegiatan.".id", $id);
		$return = $this->db->update($this->table_program_kegiatan, array($this->table_program_kegiatan.'.id_status'=>$this->id_status_approved2));
		return $return;		
	}

	function not_approved_veri_akhir_renstra($id, $data){
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();

		$this->db->where($this->table_program_kegiatan.".parent", $id);
		$this->db->or_where($this->table_program_kegiatan.".id", $id);
		$return = $this->db->update($this->table_program_kegiatan, array($this->table_program_kegiatan.'.id_status'=>$this->id_status_revisi2));

		$result = $this->db->insert("t_renstra_revisi_keg", $data); 		
		
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	function revisi_rpjmd($id_program){
		$query = "SELECT t_renstra_revisi_keg.*, t_renstra_prog_keg.kd_urusan, t_renstra_prog_keg.kd_bidang, t_renstra_prog_keg.kd_program, t_renstra_prog_keg.nama_prog_or_keg, id_renstra FROM (SELECT * FROM `t_renstra_revisi_keg` WHERE id IN (SELECT max(id) FROM t_renstra_revisi_keg GROUP BY id_prog_keg) GROUP BY id_prog_keg) AS `t_renstra_revisi_keg` INNER JOIN t_renstra_prog_keg ON t_renstra_revisi_keg.id_prog_keg=t_renstra_prog_keg.id AND t_renstra_prog_keg.is_prog_or_keg=? WHERE t_renstra_prog_keg.id=?";
		$data = array($this->is_program, $id_program);
		$result = $this->db->query($query, $data);		
		return $result->row();			
	}

	function cek_nominal_banding_dengan_rpjmd($id, $urusan, $bidang, $program, $id_renstra){
		$query = "SELECT SUM(nominal_1) AS nominal_1_pro, SUM(nominal_2) AS nominal_2_pro, SUM(nominal_3) AS nominal_3_pro, SUM(nominal_4) AS nominal_4_pro, SUM(nominal_5) AS nominal_5_pro FROM t_renstra_prog_keg WHERE is_prog_or_keg=? AND id!=? AND kd_urusan=? AND kd_bidang=? AND kd_program=? AND id_renstra=? GROUP BY kd_urusan, kd_bidang, kd_program, id_renstra";
		$data = array($this->is_kegiatan, $id, $urusan, $bidang, $program, $id_renstra);
		$result = $this->db->query($query, $data);		
		return $result->row();			
	}

	function get_total_nominal_renstra($id_skpd=NULL){
		$this->db->select('COUNT(t_renstra_prog_keg.id) AS count');
		$this->db->select_sum('nominal_1');
		$this->db->select_sum('nominal_2');
		$this->db->select_sum('nominal_3');
		$this->db->select_sum('nominal_4');
		$this->db->select_sum('nominal_5');		

		$proses = $this->count_jendela_kontrol($id_skpd);		
		if (!empty($proses->veri2)) {
			$this->db->where("id_status", $this->id_status_approved2);
		}else{
			$this->db->where("id_status", $this->id_status_approved);
		}

		if (!is_null($id_skpd) && $id_skpd != "all") {
			$this->db->where("t_renstra.id_skpd", $id_skpd);
		}		

		$this->db->where("t_renstra_prog_keg.is_prog_or_keg", $this->is_kegiatan);
		$this->db->from($this->table_program_kegiatan);
		$this->db->join($this->table,$this->table.".id = ". $this->table_program_kegiatan .".id_renstra","inner");

		$result = $this->db->get();		
		return $result->row();		
	}

	function get_all_skpd(){	
		$query = "SELECT id_skpd FROM (SELECT * FROM t_renstra_prog_keg GROUP BY id_renstra) t_renstra_prog_keg INNER JOIN t_renstra ON t_renstra.id=t_renstra_prog_keg.id_renstra";
		$result = $this->db->query($query);		
		return $result->result();		
	}

	function simpan_revisi($id_skpd, $ket){
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();

		$query = "INSERT INTO t_pengajuan_revisi(id_skpd, keterangan) VALUES (?, ?)";
		$data = array($id_skpd, $ket);
		$result = $this->db->query($query, $data);		
		
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	function check_revisi($id_skpd){
		$query = "SELECT * FROM t_pengajuan_revisi WHERE id_skpd=? AND status=1";
		$data = array($id_skpd);
		$result = $this->db->query($query, $data);		
		return $result->row();	
	}

	function get_log_revisi($id_skpd){
		$query = "SELECT * FROM t_pengajuan_revisi WHERE id_skpd=? AND status > 1";
		$data = array($id_skpd);
		$result = $this->db->query($query, $data);
		return $result->result();
	}

	function disapprove_renstra($id_renstra, $ket){
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();

		$query = "INSERT t_renstra_revisi SELECT NULL, id_renstra, id, ? FROM t_renstra_prog_keg WHERE id_renstra=?";
		$data = array($ket, $id_renstra);
		$result = $this->db->query($query, $data);

		$query = "UPDATE t_renstra_prog_keg SET id_status=3 WHERE id_renstra=?";
		$data = array($id_renstra);
		$result = $this->db->query($query, $data);
		
		$this->db->trans_complete();
		return $this->db->trans_status();
	}
}
?>