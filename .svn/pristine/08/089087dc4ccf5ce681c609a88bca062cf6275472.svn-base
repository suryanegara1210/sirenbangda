<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_kendali_belanja extends CI_Model
{
	var $table_kendali_belanja = 't_kendali_belanja_perubahan';
	var $primary_id = 'id_kendali_belanja';

	function add_kendali_belanja()
	{
		$data = $this->global_function->add_array($data, $add);

		$result = $this->db->insert($this->table_kendali_belanja, $data);
		return $result;
	}

	function get_data($data,$table){
        $this->db->where($data);
        $query = $this->db->get($this->$table);
        return $query->row();
    }

    function get_kendali_belanja_by_id($id_kendali_belanja)
	{
		$sql = "
				SELECT *
				FROM t_kendali_belanja_perubahan
				WHERE id_kendali_belanja = ?
			";

		$query = $this->db->query($sql, array($id_kendali_belanja));
		var_dump($this->query);
		if($query) {
			if($query->num_rows() > 0) {
				return $query->row();
			}
		}

		return NULL;
	}

	function simpan_kendali_belanja($data_kendali_belanja)
	{
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();


		$data_kendali_belanja->created_date		= Formatting::get_datetime();
		$data_kendali_belanja->created_by		= $this->session->userdata('username');

		$this->db->set($data_kendali_belanja);
    	$this->db->insert('t_kendali_belanja_perubahan');

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	function get_data_table($search, $start, $length, $order)
	{
		$order_arr = array('id_kendali_belanja','kd_urusan','kd_bidang','kd_program','kd_kegiatan');
		$sql="
			SELECT * FROM ".$this->table_kendali_belanja."
			WHERE kd_urusan LIKE '%".$search['value']."%'
            OR kd_bidang LIKE '%".$search['value']."%'
            OR kd_program LIKE '%".$search['value']."%'
            OR kd_kegiatan LIKE '%".$search['value']."%'
		";
		$this->db->limit($length, $start);
		$this->db->order_by($order_arr[$order["column"]], $order["dir"]);

		$result = $this->db->query($sql);
		return $result->result();
	}

	function count_data_table($search, $start, $length, $order)
	{
		$this->db->from($this->table_kendali_belanja);

		$this->db->like("kd_urusan", $search['value']);
		$this->db->or_like("kd_bidang", $search['value']);
		$this->db->or_like("kd_program", $search['value']);
		$this->db->or_like("kd_kegiatan", $search['value']);

		$result = $this->db->count_all_results();
		return $result;
	}

	function get_data_with_rincian($id_kendali_belanja,$table)
	{
		$sql="
			SELECT * FROM ".$this->$table."
			WHERE id_kendali_belanja = ?
		";

		$query = $this->db->query($sql, array($id_kendali_belanja));

		if($query) {
				if($query->num_rows() > 0) {
					return $query->row();
				}
			}

			return NULL;
	}

	function delete_kendali_belanja($id){
   	    $this->db->trans_strict(FALSE);
		$this->db->trans_start();

		$this->db->where('id_kendali_belanja',$id);
        $this->db->delete('t_kendali_belanja_perubahan');

		$this->db->trans_complete();

		return $this->db->trans_status();
    }

    function update_kendali_belanja($data,$id,$table,$primary) {
        $this->db->where($this->$primary,$id);
        return $this->db->update($this->$table,$data);
    }

	function kinerja_triwulan($id,$id_dpa_prog_keg_triwulan,$catatan,$keterangan,$capaian){
		$counter = count($catatan);

		$this->db->trans_strict(FALSE);
		$this->db->trans_start();
		//delete data yang sudah dihapus
		//cek perbeddan id
		//var_dump($id);
		//process here
		$check_new_id = 0;
		for($i=1;$i<=$counter;$i++){
			if($id[$i]!=''){
				$check_new_id++;
			}
		}

		if($check_new_id!=0){
			$list = "(";
			for($i=1;$i<=$counter;$i++){
				$list.=$id[$i];
				if($i!=$counter){
					$list.=',';
				}
			}
			$list .= ")";
			$list = "AND id NOT IN ".$list;
		}
		else{
			$list = "";
		}

		$data_delete = $this->db->query("
			SELECT id
			FROM `tx_dpa_prog_keg_triwulan_detail_perubahan`
			WHERE id_dpa_prog_keg_triwulan = '$id_dpa_prog_keg_triwulan'
			".$list)->result();

		foreach ($data_delete as $delete) {
			$this->db->delete('tx_dpa_prog_keg_triwulan_detail_perubahan', array('id' => $delete->id));
		}

		for($i=1;$i<=$counter;$i++){
			$data = array(
				'id_dpa_prog_keg_triwulan' => $id_dpa_prog_keg_triwulan,
				'catatan' => $catatan[$i],
				'keterangan' => $keterangan[$i],
				'capaian' => $capaian[$i]
			);
			if(@$id[$i]!=''){
				//ada id, update ke data sebelumnya
				$this->db->where('id', $id[$i]);
				$this->db->update('tx_dpa_prog_keg_triwulan_detail_perubahan', $data);
			}else{
				//tidak ada id, insert
				$this->db->insert('tx_dpa_prog_keg_triwulan_detail_perubahan',$data);
			}
		}


		$this->db->where('id', $id_dpa_prog_keg_triwulan)
					->update("tx_dpa_prog_keg_triwulan_perubahan", array('status_kendali' => 0));

		$this->db->trans_complete();

		return $this->db->trans_status();

	}

	/*------------------------------------------------------------------------>	
	| Verifikasi Kendali Belanja	
	------------------------------*/
	function kirim_veri($id_skpd, $ta)
	{
		$this->db->where(array('tx_dpa_prog_keg_perubahan.tahun' => $ta, 'tx_dpa_prog_keg_perubahan.id_skpd' => $id_skpd, 'tx_dpa_prog_keg_triwulan_perubahan.status_kendali' => "0"))
				->set("tx_dpa_prog_keg_triwulan_perubahan.status_kendali", "1")
				->update("tx_dpa_prog_keg_triwulan_perubahan INNER JOIN tx_dpa_prog_keg_perubahan ON tx_dpa_prog_keg_triwulan_perubahan.id_dpa_prog_keg=tx_dpa_prog_keg_perubahan.id");
	}

	function get_all_veri_list()
	{
		return $this->db
					->query("SELECT 
									m_skpd.*, COUNT(*) AS jum_semua 
								FROM tx_dpa_prog_keg_triwulan_perubahan 
								INNER JOIN tx_dpa_prog_keg_perubahan ON tx_dpa_prog_keg_perubahan.id=tx_dpa_prog_keg_triwulan_perubahan.id_dpa_prog_keg 
								INNER JOIN m_skpd ON tx_dpa_prog_keg_perubahan.id_skpd=m_skpd.id_skpd 
								WHERE 
									status_kendali=? 
								GROUP BY tx_dpa_prog_keg_perubahan.id_skpd 
								ORDER BY m_skpd.nama_skpd ASC;"
							, array('1'))
					->result();
	}

	function get_veri_list($id_skpd)
	{
		return $this->db
					->query("SELECT 
									*, tx_dpa_prog_keg_triwulan_perubahan.id AS id_tx_dpa_prog_keg_triwulan
								FROM tx_dpa_prog_keg_triwulan_perubahan 
								INNER JOIN tx_dpa_prog_keg_perubahan ON tx_dpa_prog_keg_perubahan.id=tx_dpa_prog_keg_triwulan_perubahan.id_dpa_prog_keg 
								WHERE 
									status_kendali=? AND id_skpd=?
								ORDER BY id_triwulan ASC;"
							, array('1', $id_skpd))
					->result();	
	}

	function get_detail_for_veri($id)
	{
		return $this->db
					->query("SELECT 
									a.*, b.*, c.nama_prog_or_keg AS nama_program, a.id AS id_tx_dpa_prog_keg_triwulan
								FROM tx_dpa_prog_keg_triwulan_perubahan AS a 
								INNER JOIN tx_dpa_prog_keg_perubahan AS b ON a.id_dpa_prog_keg=b.id
								INNER JOIN tx_dpa_prog_keg_perubahan AS c ON c.id=b.parent
								WHERE 
									a.id=?"
							, array($id))
					->row();	
	}

	function get_dpa_detail($id)
	{
		return $this->db
					->query("SELECT * FROM tx_dpa_prog_keg_triwulan_detail_perubahan WHERE id_dpa_prog_keg_triwulan=?"
							, array($id))
					->result();	
	}

	function approved_veri_kendali($id)
	{
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();

		$this->db->where("id", $id);
		$return = $this->db->update("tx_dpa_prog_keg_triwulan_perubahan", array('status_kendali' => 3));

		$result = $this->db->insert("tx_dpa_prog_keg_revisi_perubahan", array('id_dpa_prog_keg_triwulan' => $id, 'ket' => 'Kendali belanja perubahan disetujui', 'create_date' => date('Y-m-d H:i:s')));

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	function not_approved_veri_kendali($id, $ket)
	{
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();

		$this->db->where("id", $id);		
		$return = $this->db->update("tx_dpa_prog_keg_triwulan_perubahan", array('status_kendali' => 2));

		$result = $this->db->insert("tx_dpa_prog_keg_revisi_perubahan", array('id_dpa_prog_keg_triwulan' => $id, 'ket' => $ket, 'create_date' => date('Y-m-d H:i:s')));
		
		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	function show_revisi($id)
	{
		return $this->db
					->query("SELECT *, DATE_FORMAT(create_date, '%d/%m/%Y %H:%i') AS formated_date
								FROM tx_dpa_prog_keg_revisi_perubahan
								WHERE 
									id_dpa_prog_keg_triwulan=?
								ORDER BY create_date DESC;"
							, array($id))
					->result();			
	}
}
