<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_evaluasi_renja extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

	public function get_periode()
	{
		$data = $this->db->select("m_periode_evaluasi_renja.*, IF(date_start<=NOW() AND NOW()<=date_end, 1, 0) active", false)
						->from("m_periode_evaluasi_renja")
						->get()
						->result();
		$return = array();
		foreach ($data as $row) {
			$return[$row->triwulan] = $row;
		}

		return $return;
	}

	public function get_renja_all($tahun, $id_skpd)
	{
		$where = array(
						't_renja_prog_keg.tahun' => $tahun,
						't_renja_prog_keg.id_skpd' => $id_skpd,
						'id_status' => 7
					);
		$data['renja'] = $this->db->where($where)
									->from("t_renja_prog_keg")
                  ->order_by("kd_urusan, kd_bidang, kd_program, kd_kegiatan, is_prog_or_keg")
                  ->get()
									->result();

		$indikator = $this->db->where($where)
        									->where("target <> 0")
        									->select("t_renja_indikator_prog_keg.*")
        									->select("m_lov.nama_value AS satuan", false)
        									->select("tw_1.id AS tw_1, tw_2.id AS tw_2, tw_3.id AS tw_3, tw_4.id AS tw_4", false)
        									->select("tw_1.status AS status_1, tw_2.status AS status_2, tw_3.status AS status_3, tw_4.status AS status_4", false)
                          ->from("t_renja_indikator_prog_keg")
        									->join("t_renja_prog_keg", "t_renja_indikator_prog_keg.id_prog_keg=t_renja_prog_keg.id", "inner")
        									->join("m_lov", "t_renja_indikator_prog_keg.satuan_target=m_lov.kode_value AND nama_app='satuan'", "left")
        									->join("vw_evaluasi_renja_tw AS tw_1", "tw_1.id_indikator_prog_keg=t_renja_indikator_prog_keg.id AND tw_1.tahun=t_renja_prog_keg.tahun AND tw_1.triwulan=1", "left")
        									->join("vw_evaluasi_renja_tw AS tw_2", "tw_2.id_indikator_prog_keg=t_renja_indikator_prog_keg.id AND tw_2.tahun=t_renja_prog_keg.tahun AND tw_2.triwulan=2", "left")
        									->join("vw_evaluasi_renja_tw AS tw_3", "tw_3.id_indikator_prog_keg=t_renja_indikator_prog_keg.id AND tw_3.tahun=t_renja_prog_keg.tahun AND tw_3.triwulan=3", "left")
        									->join("vw_evaluasi_renja_tw AS tw_4", "tw_4.id_indikator_prog_keg=t_renja_indikator_prog_keg.id AND tw_4.tahun=t_renja_prog_keg.tahun AND tw_4.triwulan=4", "left")
        									->order_by("t_renja_indikator_prog_keg.id_prog_keg")
        									->get()
        									->result();

    $indikator_cik = $this->db->where(array(
                                              'tx_cik_prog_keg.tahun' => $tahun,
                                              'tx_cik_prog_keg.id_skpd' => $id_skpd
                                            )
                                      )
        									->where("tx_cik_indikator_prog_keg.target <> 0")
                          ->where("indikator_rka.id_indikator_renja IS NULL")
        									->select("tx_cik_indikator_prog_keg.*")
                          ->select("renja.id AS id_prog_keg_renja")
        									->select("m_lov.nama_value AS satuan", false)
                          ->select("tw_1.id AS tw_1, tw_2.id AS tw_2, tw_3.id AS tw_3, tw_4.id AS tw_4", false)
        									->select("tw_1.status AS status_1, tw_2.status AS status_2, tw_3.status AS status_3, tw_4.status AS status_4", false)
                          ->from("tx_cik_indikator_prog_keg")
        									->join("tx_cik_prog_keg", "tx_cik_indikator_prog_keg.id_prog_keg=tx_cik_prog_keg.id", "inner")
                          ->join("tx_dpa_prog_keg AS dpa", "tx_cik_prog_keg.id_dpa=dpa.id", "left")
                          ->join("tx_rka_prog_keg AS rka", "dpa.id_rka=rka.id", "left")
                          ->join("t_renja_prog_keg AS renja", "rka.id_renja=renja.id", "left")
                          ->join("tx_dpa_indikator_prog_keg AS indikator_dpa", "tx_cik_indikator_prog_keg.id_indikator_dpa=indikator_dpa.id AND indikator_dpa.id_prog_keg=dpa.id", "left")
                          ->join("tx_rka_indikator_prog_keg AS indikator_rka", "indikator_dpa.id_indikator_rka=indikator_rka.id AND indikator_rka.id_prog_keg=rka.id", "left")
        									->join("m_lov", "tx_cik_indikator_prog_keg.satuan_target=m_lov.kode_value AND nama_app='satuan'", "left")
                          ->join("vw_evaluasi_renja_tw AS tw_1", "tw_1.id_indikator_prog_keg_cik=tx_cik_indikator_prog_keg.id AND tw_1.tahun=tx_cik_prog_keg.tahun AND tw_1.triwulan=1", "left")
        									->join("vw_evaluasi_renja_tw AS tw_2", "tw_2.id_indikator_prog_keg_cik=tx_cik_indikator_prog_keg.id AND tw_2.tahun=tx_cik_prog_keg.tahun AND tw_2.triwulan=2", "left")
        									->join("vw_evaluasi_renja_tw AS tw_3", "tw_3.id_indikator_prog_keg_cik=tx_cik_indikator_prog_keg.id AND tw_3.tahun=tx_cik_prog_keg.tahun AND tw_3.triwulan=3", "left")
        									->join("vw_evaluasi_renja_tw AS tw_4", "tw_4.id_indikator_prog_keg_cik=tx_cik_indikator_prog_keg.id AND tw_4.tahun=tx_cik_prog_keg.tahun AND tw_4.triwulan=4", "left")
        									->order_by("tx_cik_indikator_prog_keg.id_prog_keg")
        									->get()
        									->result();

		$temp = array();
		$jumlah = array();

    #tipe_ind = 1 -> dari renja
    #tipe_ind = 2 -> dari cik
		foreach ($indikator as $row) {
      $temp_row = $row;
      $temp_row->tipe_ind = 1;

			$temp[$row->id_prog_keg][] = $temp_row;

			if (empty($jumlah[$row->id_prog_keg])) {
				$jumlah[$row->id_prog_keg]=1;
			}else{
				$jumlah[$row->id_prog_keg]++;
			}
		}

    foreach ($indikator_cik as $row) {
      $temp_row = $row;
      $temp_row->tipe_ind = 2;

			$temp[$row->id_prog_keg_renja][] = $temp_row;

			if (empty($jumlah[$row->id_prog_keg_renja])) {
				$jumlah[$row->id_prog_keg_renja]=1;
			}else{
				$jumlah[$row->id_prog_keg_renja]++;
			}
		}
		$data['indikator'] = $temp;
		$data['jumlah_indikator'] = $jumlah;

		return $data;
	}

  public function get_cik_all($tahun, $id_skpd)
	{
    $data['cik'] = $this->db->query("SELECT
                                tx_cik_prog_keg.*
                              FROM
                                tx_cik_prog_keg
                                LEFT JOIN tx_dpa_prog_keg
                                  ON tx_cik_prog_keg.id_dpa = tx_dpa_prog_keg.id
                                LEFT JOIN tx_rka_prog_keg
                                  ON tx_dpa_prog_keg.id_rka = tx_rka_prog_keg.id
                                LEFT JOIN t_renja_prog_keg
                                  ON tx_rka_prog_keg.id_renja = t_renja_prog_keg.id
                              WHERE tx_cik_prog_keg.tahun = ?
                                AND tx_cik_prog_keg.id_skpd = ?
                                AND t_renja_prog_keg.id IS NULL;",
                                array($tahun, $id_skpd)
                            )
                    ->result();

    $indikator = $this->db->query("SELECT
                                      tx_cik_indikator_prog_keg.*,
                                      m_lov.nama_value AS satuan,
                                      tw_1.id AS tw_1, tw_2.id AS tw_2, tw_3.id AS tw_3, tw_4.id AS tw_4,
                                      tw_1.status AS status_1, tw_2.status AS status_2, tw_3.status AS status_3, tw_4.status AS status_4
                                    FROM
                                      tx_cik_indikator_prog_keg
                                      INNER JOIN tx_cik_prog_keg
                                        ON tx_cik_prog_keg.id = tx_cik_indikator_prog_keg.id_prog_keg
                                      LEFT JOIN tx_dpa_prog_keg
                                        ON tx_cik_prog_keg.id_dpa = tx_dpa_prog_keg.id
                                      LEFT JOIN tx_rka_prog_keg
                                        ON tx_dpa_prog_keg.id_rka = tx_rka_prog_keg.id
                                      LEFT JOIN t_renja_prog_keg
                                        ON tx_rka_prog_keg.id_renja = t_renja_prog_keg.id
                                      LEFT JOIN `m_lov`
                                        ON `tx_cik_indikator_prog_keg`.`satuan_target`=`m_lov`.`kode_value` AND nama_app='satuan'
                                      LEFT JOIN `vw_evaluasi_renja_tw` AS tw_1
                                        ON tw_1.id_indikator_prog_keg_cik=tx_cik_indikator_prog_keg.id AND tw_1.tahun=tx_cik_prog_keg.tahun AND tw_1.triwulan=1
                                      LEFT JOIN `vw_evaluasi_renja_tw` AS tw_2
                                        ON tw_2.id_indikator_prog_keg_cik=tx_cik_indikator_prog_keg.id AND tw_2.tahun=tx_cik_prog_keg.tahun AND tw_2.triwulan=2
                                      LEFT JOIN `vw_evaluasi_renja_tw` AS tw_3
                                        ON tw_3.id_indikator_prog_keg_cik=tx_cik_indikator_prog_keg.id AND tw_3.tahun=tx_cik_prog_keg.tahun AND tw_3.triwulan=3
                                      LEFT JOIN `vw_evaluasi_renja_tw` AS tw_4
                                        ON tw_4.id_indikator_prog_keg_cik=tx_cik_indikator_prog_keg.id AND tw_4.tahun=tx_cik_prog_keg.tahun AND tw_4.triwulan=4
                                    WHERE tx_cik_prog_keg.tahun = ?
                                      AND tx_cik_prog_keg.id_skpd = ?
                                      AND t_renja_prog_keg.id IS NULL;",
                                      array($tahun, $id_skpd)
                                  )
                          ->result();

    $temp = array();
  	$jumlah = array();

  	foreach ($indikator as $row) {
      $temp_row = $row;

  		$temp[$row->id_prog_keg][] = $temp_row;

  		if (empty($jumlah[$row->id_prog_keg])) {
  			$jumlah[$row->id_prog_keg]=1;
  		}else{
  			$jumlah[$row->id_prog_keg]++;
  		}
    }

    $data['indikator'] = $temp;
		$data['jumlah_indikator'] = $jumlah;

    $keg_temp = array();
    $row_ciks = $data['cik'];
    for ($i=0; $i < count($row_ciks); $i++) {
      $row_cik = $row_ciks[$i];
      $keg_temp[$row_cik->kd_urusan][$row_cik->kd_bidang][$row_cik->kd_program][] = array('number' => $i, 'value' => $row_cik);
    }
    $data['keg_row'] = $keg_temp;

		return $data;
  }

	public function get_one_renja($id)
	{
		$where = array(
						't_renja_prog_keg.id' => $id
					);
		$data['prog_keg'] = $this->db->where($where)
									->get("t_renja_prog_keg")
									->row();

		$indikator = $this->db->where($where)
									->select("t_renja_indikator_prog_keg.*")
									->select("m_lov.nama_value AS satuan", false)
									->join("t_renja_prog_keg", "t_renja_indikator_prog_keg.id_prog_keg=t_renja_prog_keg.id", "inner")
									->join("m_lov", "t_renja_indikator_prog_keg.satuan_target=m_lov.kode_value AND nama_app='satuan'", "left")
									->order_by("t_renja_indikator_prog_keg.id_prog_keg")
									->get("t_renja_indikator_prog_keg")
									->result();

		$data['indikator'] = $indikator;

		return $data;
	}

  public function get_one_cik($id)
	{
		$where = array(
						'tx_cik_prog_keg.id' => $id
					);
		$data['prog_keg'] = $this->db->where($where)
									->get("tx_cik_prog_keg")
									->row();

		$indikator = $this->db->where($where)
									->select("tx_cik_indikator_prog_keg.*")
									->select("m_lov.nama_value AS satuan", false)
									->join("tx_cik_prog_keg", "tx_cik_indikator_prog_keg.id_prog_keg=tx_cik_prog_keg.id", "inner")
									->join("m_lov", "tx_cik_indikator_prog_keg.satuan_target=m_lov.kode_value AND nama_app='satuan'", "left")
									->order_by("tx_cik_indikator_prog_keg.id_prog_keg")
									->get("tx_cik_indikator_prog_keg")
									->result();

		$data['indikator'] = $indikator;

		return $data;
	}

	public function get_max_tahun()
	{
		return $this->db->query("SELECT MAX(tahun_anggaran) AS max FROM m_tahun_anggaran")->row()->max;
	}

	public function get_tahun_now($tahun)
	{
		return $this->db->query("SELECT * FROM m_tahun_anggaran WHERE tahun_anggaran = '". $tahun ."'")->row();
	}

	public function get_less_tahun($tahun)
	{
		$data = $this->db->query("SELECT * FROM m_tahun_anggaran WHERE tahun_anggaran < '". $tahun ."' ORDER BY tahun_anggaran DESC")->row();
		if (empty($data)) {
			return NULL;
		}

		return $data->tahun_anggaran;
	}

	public function get_indikator_renstra($id_indikator_renja)
	{
    ######## Old ########
		// $data = $this->db->select("t_renja_indikator_prog_keg.*")
		// 					->select("t_renja_prog_keg.id_renstra")
		// 					->where("t_renja_indikator_prog_keg.id", $id_indikator_renja)
		// 					->join("t_renja_prog_keg", "t_renja_prog_keg.id=t_renja_indikator_prog_keg.id_prog_keg", "inner")
		// 					->get("t_renja_indikator_prog_keg")
		// 					->row();
    //
		// return $this->db->where("indikator", $data->indikator)
		// 					->where("id_prog_keg", $data->id_renstra)
		// 					->select("t_renstra_indikator_prog_keg.*")
		// 					->select("m_lov.nama_value AS satuan", false)
		// 					->join("m_lov", "t_renstra_indikator_prog_keg.satuan_target=m_lov.kode_value AND nama_app='satuan'", "left")
		// 					->get("t_renstra_indikator_prog_keg")
		// 					->row();

    return $this->db->query("SELECT t_renstra_indikator_prog_keg.*, m_lov.nama_value AS satuan FROM t_renja_indikator_prog_keg LEFT JOIN t_renstra_indikator_prog_keg ON t_renstra_indikator_prog_keg.id=t_renja_indikator_prog_keg.id_indikator_renstra LEFT JOIN m_lov ON t_renstra_indikator_prog_keg.satuan_target=m_lov.kode_value AND nama_app='satuan' WHERE t_renja_indikator_prog_keg.id=?;", array($id_indikator_renja))
                    ->row();
	}

  public function get_indikator_renstra_cik($id_indikator_cik)
	{
    return $this->db->query("SELECT t_renstra_indikator_prog_keg.*, m_lov.nama_value AS satuan FROM tx_cik_indikator_prog_keg LEFT JOIN tx_dpa_indikator_prog_keg ON tx_dpa_indikator_prog_keg.id=tx_cik_indikator_prog_keg.id_indikator_dpa LEFT JOIN tx_rka_indikator_prog_keg ON tx_rka_indikator_prog_keg.id=tx_dpa_indikator_prog_keg.id_indikator_rka LEFT JOIN t_renja_indikator_prog_keg ON t_renja_indikator_prog_keg.id=tx_rka_indikator_prog_keg.id_indikator_renja LEFT JOIN t_renstra_indikator_prog_keg ON t_renstra_indikator_prog_keg.id=t_renja_indikator_prog_keg.id_indikator_renstra LEFT JOIN m_lov ON t_renstra_indikator_prog_keg.satuan_target=m_lov.kode_value AND nama_app='satuan' WHERE tx_cik_indikator_prog_keg.id=?;", array($id_indikator_cik))
                    ->row();
	}

	public function get_renstra($id_renja)
	{
		return $this->db->where("t_renja_prog_keg.id", $id_renja)
							->select("t_renstra_prog_keg.*")
							->join("t_renstra_prog_keg", "t_renstra_prog_keg.id=t_renja_prog_keg.id_renstra", "inner")
							->get("t_renja_prog_keg")
							->row();
	}

  public function get_renstra_cik($id_cik)
	{
		return $this->db->query("SELECT * FROM tx_cik_prog_keg LEFT JOIN tx_dpa_prog_keg ON tx_dpa_prog_keg.id=tx_cik_prog_keg.id_dpa LEFT JOIN tx_rka_prog_keg ON tx_rka_prog_keg.id=tx_dpa_prog_keg.id_rka LEFT JOIN t_renja_prog_keg ON t_renja_prog_keg.id=tx_rka_prog_keg.id_renja LEFT JOIN t_renstra_prog_keg ON t_renstra_prog_keg.id=t_renja_prog_keg.id_renstra WHERE tx_cik_prog_keg.id=?;", array($id_cik))
							      ->row();
	}

  public function check_renja_cik($id_indikator_cik)
	{
    return $this->db->query("SELECT t_renja_indikator_prog_keg.*, t_renja_prog_keg.* FROM tx_cik_indikator_prog_keg LEFT JOIN tx_dpa_indikator_prog_keg ON tx_dpa_indikator_prog_keg.id=tx_cik_indikator_prog_keg.id_indikator_dpa LEFT JOIN tx_rka_indikator_prog_keg ON tx_rka_indikator_prog_keg.id=tx_dpa_indikator_prog_keg.id_indikator_rka LEFT JOIN t_renja_indikator_prog_keg ON t_renja_indikator_prog_keg.id=tx_rka_indikator_prog_keg.id_indikator_renja LEFT JOIN t_renja_prog_keg ON t_renja_prog_keg.id=t_renja_indikator_prog_keg.id_prog_keg WHERE t_renja_prog_keg.id IS NOT NULL AND t_renja_indikator_prog_keg.id IS NOT NULL AND tx_cik_indikator_prog_keg.id=?;", array($id_indikator_cik))
                    ->row();
	}

	public function get_renja($id_renja)
	{
		return $this->db->where("t_renja_prog_keg.id", $id_renja)
							->get("t_renja_prog_keg")
							->row();
	}

	public function get_cik($id_renja)
	{
		$renja = $this->get_renja($id_renja);
		return $this->db->where("kd_urusan", $renja->kd_urusan)
							->where("kd_bidang", $renja->kd_bidang)
							->where("kd_program", $renja->kd_program)
							->where("kd_kegiatan", $renja->kd_kegiatan)
							->where("tahun", $renja->tahun)
							->where("id_skpd", $renja->id_skpd)
							->get("tx_cik_prog_keg")
							->row();
	}

  public function get_cik_itself($id_cik)
	{
		return $this->db->where("id", $id_cik)
      							->get("tx_cik_prog_keg")
      							->row();
	}

	public function get_indikator_cik($id_indikator_renja)
	{
    ######## Old ########
		// $data = $this->db->select("t_renja_indikator_prog_keg.*")
		// 					->select("t_renja_prog_keg.id_renstra, kd_urusan, kd_bidang, kd_program, kd_kegiatan, tahun, id_skpd")
		// 					->where("t_renja_indikator_prog_keg.id", $id_indikator_renja)
		// 					->join("t_renja_prog_keg", "t_renja_prog_keg.id=t_renja_indikator_prog_keg.id_prog_keg", "inner")
		// 					->get("t_renja_indikator_prog_keg")
		// 					->row();
    //
		// return $this->db->where("indikator", $data->indikator)
		// 					->where("kd_urusan", $data->kd_urusan)
		// 					->where("kd_bidang", $data->kd_bidang)
		// 					->where("kd_program", $data->kd_program)
		// 					->where("kd_kegiatan", $data->kd_kegiatan)
		// 					->where("tahun", $data->tahun)
		// 					->where("id_skpd", $data->id_skpd)
		// 					->select("tx_cik_indikator_prog_keg.*")
		// 					->select("m_lov.nama_value AS satuan", false)
		// 					->join("m_lov", "tx_cik_indikator_prog_keg.satuan_target=m_lov.kode_value AND nama_app='satuan'", "left")
		// 					->join("tx_cik_prog_keg", "tx_cik_prog_keg.id=tx_cik_indikator_prog_keg.id_prog_keg", "inner")
		// 					->get("tx_cik_indikator_prog_keg")
		// 					->row();

    return $this->db->query("SELECT tx_cik_indikator_prog_keg.*, m_lov.nama_value AS satuan FROM tx_cik_indikator_prog_keg LEFT JOIN `m_lov` ON `tx_cik_indikator_prog_keg`.`satuan_target`=`m_lov`.`kode_value` AND nama_app='satuan' LEFT JOIN tx_dpa_indikator_prog_keg ON tx_cik_indikator_prog_keg.id_indikator_dpa=tx_dpa_indikator_prog_keg.id LEFT JOIN tx_rka_indikator_prog_keg ON tx_dpa_indikator_prog_keg.id_indikator_rka=tx_rka_indikator_prog_keg.id LEFT JOIN t_renja_indikator_prog_keg ON t_renja_indikator_prog_keg.id=tx_rka_indikator_prog_keg.id_indikator_renja WHERE t_renja_indikator_prog_keg.id=?;", array($id_indikator_renja))
                    ->row();
	}

  public function get_indikator_cik_itself($id_indikator_cik)
	{
    return $this->db->query("SELECT tx_cik_indikator_prog_keg.*, m_lov.nama_value AS satuan FROM tx_cik_indikator_prog_keg LEFT JOIN `m_lov` ON `tx_cik_indikator_prog_keg`.`satuan_target`=`m_lov`.`kode_value` AND nama_app='satuan' WHERE tx_cik_indikator_prog_keg.id=?;", array($id_indikator_cik))
                    ->row();
	}

	public function get_last_evaluasi_renja($id_indikator, $tahun)
	{
		$data = $this->db->select("t_evaluasi_renja.*")
							->where("t_evaluasi_renja.id_indikator_prog_keg", $id_indikator)
							->where("t_evaluasi_renja_prog_keg.tahun <=".$tahun)
              ->where("t_evaluasi_renja_prog_keg.triwulan", '4')
              ->join("t_evaluasi_renja_prog_keg", "t_evaluasi_renja.id_evaluasi_renja_prog_keg=t_evaluasi_renja_prog_keg.id")
							->get("t_evaluasi_renja")
							->row();
	}

  public function get_last_evaluasi_renja_cik($id_indikator, $tahun)
	{
		$data = $this->db->select("t_evaluasi_renja.*")
							->where("t_evaluasi_renja.id_indikator_prog_keg_cik", $id_indikator)
              ->where("t_evaluasi_renja_prog_keg.tahun <=".$tahun)
              ->where("t_evaluasi_renja_prog_keg.triwulan", '4')
              ->join("t_evaluasi_renja_prog_keg", "t_evaluasi_renja.id_evaluasi_renja_prog_keg=t_evaluasi_renja_prog_keg.id")
							->get("t_evaluasi_renja")
							->row();
	}

  public function save($data_evaluasi_prog_keg, $realisasi, $data_evaluasi, $id_evaluasi_renja_before)
	{
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();

    $where_test = array(
                          "kd_urusan" => $data_evaluasi_prog_keg["kd_urusan"],
                          "kd_bidang" => $data_evaluasi_prog_keg["kd_bidang"],
                          "kd_program" => $data_evaluasi_prog_keg["kd_program"],
                          "id_skpd" => $data_evaluasi_prog_keg["id_skpd"],
                          "is_prog_or_keg" => $data_evaluasi_prog_keg["is_prog_or_keg"],
                          "tahun" => $data_evaluasi_prog_keg["tahun"],
                          "triwulan" => $data_evaluasi_prog_keg["triwulan"],
                          "kd_kegiatan" => NULL,
                        );
    if (!empty($data_evaluasi_prog_keg["kd_kegiatan"])) {
      $where_test['kd_kegiatan'] = $data_evaluasi_prog_keg["kd_kegiatan"];
    }
    $test_prog_keg = $this->db->where($where_test)
                              ->from("t_evaluasi_renja_prog_keg")
                              ->get()
                              ->row();
    if (!empty($test_prog_keg)) {
      $id_evaluasi_renja = $test_prog_keg->id;
      $this->db->where("id", $id_evaluasi_renja);
      $this->db->update("t_evaluasi_renja_prog_keg", $data_evaluasi_prog_keg);

      $this->db->where("id_evaluasi_renja_prog_keg", $id_evaluasi_renja);
      $this->db->delete("t_evaluasi_renja_realisasi_kinerja_tw_prog_keg");
    }else{
      $this->db->insert("t_evaluasi_renja_prog_keg", $data_evaluasi_prog_keg);
      $id_evaluasi_renja = $this->db->insert_id();
    }

		$insert_realisasi =  array();
		foreach ($realisasi['rp'] as $key => $value) {
			$insert_realisasi[] = array(
											'id_evaluasi_renja_prog_keg' => $id_evaluasi_renja,
											'triwulan' => $key,
											'realisasi_rp' => $value,
										);
		}
		$this->db->insert_batch('t_evaluasi_renja_realisasi_kinerja_tw_prog_keg', $insert_realisasi);

    $test_evaluasi = $this->db->where(array("id" => $id_evaluasi_renja_before))
                              ->from("t_evaluasi_renja")
                              ->get()
                              ->row();
    if (!empty($test_evaluasi)) {
      // Save the data as correction
      if ($test_evaluasi->status == 4) {
        $data_evaluasi['status'] = 2;
      }

      $this->db->where("id", $id_evaluasi_renja_before);
      $this->db->update("t_evaluasi_renja", $data_evaluasi);
      $id_evaluasi_renja_det = $id_evaluasi_renja_before;

      $this->db->where("id_evaluasi_renja", $id_evaluasi_renja_det);
      $this->db->delete("t_evaluasi_renja_realisasi_kinerja_tw");
    }else{
      $data_evaluasi['id_evaluasi_renja_prog_keg'] = $id_evaluasi_renja;
      $this->db->insert("t_evaluasi_renja", $data_evaluasi);
      $id_evaluasi_renja_det = $this->db->insert_id();
    }

    $insert_realisasi =  array();
		foreach ($realisasi['k'] as $key => $value) {
			$insert_realisasi[] = array(
											'id_evaluasi_renja' => $id_evaluasi_renja_det,
											'triwulan' => $key,
											'realisasi_k' => $value,
										);
		}
		$this->db->insert_batch('t_evaluasi_renja_realisasi_kinerja_tw', $insert_realisasi);

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function get_one_evaluasi_renja($id_indikator, $tahun, $tw)
	{
		return $this->db->select("t_evaluasi_renja.*, t_evaluasi_renja_prog_keg.*, t_evaluasi_renja.id AS id_evaluasi_renja, t_evaluasi_renja_prog_keg.id AS id_evaluasi_renja_prog_keg")
      							->from("t_evaluasi_renja")
                    ->join("t_evaluasi_renja_prog_keg", "t_evaluasi_renja.id_evaluasi_renja_prog_keg=t_evaluasi_renja_prog_keg.id")
                    ->where("t_evaluasi_renja.id_indikator_prog_keg", $id_indikator)
                    ->where("t_evaluasi_renja_prog_keg.tahun", $tahun)
                    ->where("t_evaluasi_renja_prog_keg.triwulan", $tw)
                    ->get()
      							->row();
	}

  public function get_one_evaluasi_renja_cik($id_indikator, $tahun, $tw)
	{
		return $this->db->select("t_evaluasi_renja.*, t_evaluasi_renja_prog_keg.*, t_evaluasi_renja.id AS id_evaluasi_renja, t_evaluasi_renja_prog_keg.id AS id_evaluasi_renja_prog_keg")
      							->from("t_evaluasi_renja")
                    ->join("t_evaluasi_renja_prog_keg", "t_evaluasi_renja.id_evaluasi_renja_prog_keg=t_evaluasi_renja_prog_keg.id")
                    ->where("t_evaluasi_renja.id_indikator_prog_keg_cik", $id_indikator)
                    ->where("t_evaluasi_renja_prog_keg.tahun", $tahun)
                    ->where("t_evaluasi_renja_prog_keg.triwulan", $tw)
                    ->get()
      							->row();
	}

	public function get_one_evaluasi_detail($id)
	{
    $data['evaluasirenja'] =  $this->db->query("SELECT
                                          t_evaluasi_renja.*, t_evaluasi_renja_prog_keg.*, m_lov.nama_value AS satuan, tx_cik_indikator_prog_keg.id_prog_keg AS id_cik, t_renja_indikator_prog_keg.id_prog_keg AS id_renja
                                        FROM
                                          t_evaluasi_renja
                                        INNER JOIN
                                          t_evaluasi_renja_prog_keg ON t_evaluasi_renja.id_evaluasi_renja_prog_keg=t_evaluasi_renja_prog_keg.id
												                LEFT JOIN
                                          m_lov ON t_evaluasi_renja.satuan=m_lov.kode_value AND nama_app='satuan'
                                        LEFT JOIN
                                          tx_cik_indikator_prog_keg ON t_evaluasi_renja.id_indikator_prog_keg_cik=tx_cik_indikator_prog_keg.id
                                        LEFT JOIN
                                          t_renja_indikator_prog_keg ON t_evaluasi_renja.id_indikator_prog_keg=t_renja_indikator_prog_keg.id
												                WHERE
                                          t_evaluasi_renja.id=?
												                LIMIT 1",
												                array($id)
											                )->row();

    $realisasi = $this->db->from("t_evaluasi_renja_realisasi_kinerja_tw")
          								->where("id_evaluasi_renja", $id)
          								->get()
          								->result();

		$realisasi_arr = array();
		foreach ($realisasi as $row) {
			$realisasi_arr[$row->triwulan] = $row;
		}

    $realisasi_prog_keg = $this->db->select("t_evaluasi_renja_realisasi_kinerja_tw_prog_keg.*")
                          ->from("t_evaluasi_renja_realisasi_kinerja_tw_prog_keg")
                          ->join("t_evaluasi_renja_prog_keg", "t_evaluasi_renja_realisasi_kinerja_tw_prog_keg.id_evaluasi_renja_prog_keg=t_evaluasi_renja_prog_keg.id", "inner")
                          ->join("t_evaluasi_renja", "t_evaluasi_renja.id_evaluasi_renja_prog_keg=t_evaluasi_renja_prog_keg.id", "inner")
          								->where("t_evaluasi_renja.id", $id)
          								->get()
          								->result();

		$realisasi_prog_keg_arr = array();
		foreach ($realisasi_prog_keg as $row) {
			$realisasi_prog_keg_arr[$row->triwulan] = $row;
		}

    $indikator = $this->db->query("SELECT t_evaluasi_renja.*, m_lov.nama_value AS satuan_target FROM t_evaluasi_renja LEFT JOIN m_lov ON t_evaluasi_renja.satuan=m_lov.kode_value AND nama_app='satuan' WHERE id_evaluasi_renja_prog_keg=?;", array($data['evaluasirenja']->id_evaluasi_renja_prog_keg))->result();

    $data['indikator'] = $indikator;
		$data['realisasi'] = $realisasi_arr;
    $data['realisasi_prog_keg'] = $realisasi_prog_keg_arr;

		return $data;
	}

	public function get_one_evaluasi_detail_berjalan_by_id($id, $tw)
	{
		return $this->db->where("t_evaluasi_renja_detail_berjalan.id_evaluasi_renja", $id)
							->where("t_evaluasi_renja_detail_berjalan.triwulan", $tw)
							->from("t_evaluasi_renja_detail_berjalan")
							->get()
							->row();
	}

	public function get_data_need_veri($id_skpd, $tahun)
	{
		$where = array(
						't_evaluasi_renja_prog_keg.tahun' => $tahun,
						'id_skpd' => $id_skpd
					);

		return $this->db->select("t_evaluasi_renja.*, t_evaluasi_renja_prog_keg.triwulan, m_status_evaluasi_renja.status AS nama_status, COUNT(*) AS jumlah")
						->from("t_evaluasi_renja")
						->join("t_evaluasi_renja_prog_keg", "t_evaluasi_renja.id_evaluasi_renja_prog_keg=t_evaluasi_renja_prog_keg.id", "inner")
						->join("m_status_evaluasi_renja", "t_evaluasi_renja.status=m_status_evaluasi_renja.id", "inner")
						->group_by("t_evaluasi_renja_prog_keg.triwulan, t_evaluasi_renja.status")
						->order_by("t_evaluasi_renja_prog_keg.triwulan, t_evaluasi_renja.status")
						->where("(t_evaluasi_renja.status=1 OR t_evaluasi_renja.status=2)")
						->where($where)
						->get()
						->result();
	}

	public function kirim_veri($id_skpd, $tahun, $triwulan, $status)
	{
		$this->db->query("UPDATE t_evaluasi_renja INNER JOIN t_evaluasi_renja_prog_keg ON t_evaluasi_renja.id_evaluasi_renja_prog_keg=t_evaluasi_renja_prog_keg.id INNER JOIN m_status_evaluasi_renja ON t_evaluasi_renja.status=m_status_evaluasi_renja.id SET t_evaluasi_renja.status=3 WHERE id_skpd=? AND t_evaluasi_renja_prog_keg.tahun=? AND t_evaluasi_renja_prog_keg.triwulan=? AND t_evaluasi_renja.status=?;", array($id_skpd, $tahun, $triwulan, $status));
	}

	### Verifikasi ###
	public function get_skpd_veri_data($tahun)
	{
		$where = array(
						't_evaluasi_renja_prog_keg.tahun' => $tahun
					);

		return $this->db->select("t_evaluasi_renja_prog_keg.*, nama_skpd, COUNT(*) AS jumlah")
						->from("t_evaluasi_renja")
						->join("t_evaluasi_renja_prog_keg", "t_evaluasi_renja.id_evaluasi_renja_prog_keg=t_evaluasi_renja_prog_keg.id", "inner")
						->join("m_skpd", "t_evaluasi_renja_prog_keg.id_skpd=m_skpd.id_skpd", "inner")
						->group_by("t_evaluasi_renja_prog_keg.id_skpd, t_evaluasi_renja_prog_keg.triwulan")
						->order_by("t_evaluasi_renja_prog_keg.id_skpd, t_evaluasi_renja_prog_keg.triwulan")
						->where("t_evaluasi_renja.status", 3)
						->where($where)
						->get()
						->result();
	}

	public function get_table_veri_skpd($tahun, $id_skpd, $triwulan)
	{
		$where = array(
						't_evaluasi_renja_prog_keg.tahun' => $tahun,
						't_evaluasi_renja_prog_keg.id_skpd' => $id_skpd,
            't_evaluasi_renja_prog_keg.triwulan' => $triwulan
					);

		$data['evaluasi_renja_prog_keg'] = $this->db->where($where)
                              									->from("t_evaluasi_renja_prog_keg")
                                                ->order_by("kd_urusan, kd_bidang, kd_program, kd_kegiatan, is_prog_or_keg")
                                                ->get()
                              									->result();

		$indikator = $this->db->where($where)
									->select("t_evaluasi_renja.*")
									->select("m_lov.nama_value AS satuan", false)
                  ->from("t_evaluasi_renja")
									->join("t_evaluasi_renja_prog_keg", "t_evaluasi_renja.id_evaluasi_renja_prog_keg=t_evaluasi_renja_prog_keg.id", "inner")
									->join("m_lov", "t_evaluasi_renja.satuan=m_lov.kode_value AND nama_app='satuan'", "left")
									->order_by("t_evaluasi_renja_prog_keg.id")
									->get()
									->result();

		$temp = array();
		$jumlah = array();
		foreach ($indikator as $row) {
			$temp[$row->id_evaluasi_renja_prog_keg][] = $row;

			if (empty($jumlah[$row->id_evaluasi_renja_prog_keg])) {
				$jumlah[$row->id_evaluasi_renja_prog_keg]=1;
			}else{
				$jumlah[$row->id_evaluasi_renja_prog_keg]++;
			}
		}
		$data['evaluasi_renja'] = $temp;
		$data['jumlah_evaluasi_renja'] = $jumlah;

    $realisasi = $this->db->where($where)
                          ->select("t_evaluasi_renja_realisasi_kinerja_tw_prog_keg.*")
        									->from("t_evaluasi_renja_prog_keg")
                          ->join("t_evaluasi_renja_realisasi_kinerja_tw_prog_keg", "t_evaluasi_renja_realisasi_kinerja_tw_prog_keg.id_evaluasi_renja_prog_keg=t_evaluasi_renja_prog_keg.id", "inner")
                          ->order_by("t_evaluasi_renja_prog_keg.id")
                          ->get()
        									->result();

		$temp = array();
		foreach ($realisasi as $row) {
			$temp[$row->id_evaluasi_renja_prog_keg][$row->triwulan] = $row;
		}
		$data['realisasi_evaluasi_renja_prog_keg'] = $temp;

		$realisasi = $this->db->where($where)
									->select("t_evaluasi_renja_realisasi_kinerja_tw.*")
                  ->from("t_evaluasi_renja")
									->join("t_evaluasi_renja_prog_keg", "t_evaluasi_renja.id_evaluasi_renja_prog_keg=t_evaluasi_renja_prog_keg.id", "inner")
                  ->join("t_evaluasi_renja_realisasi_kinerja_tw", "t_evaluasi_renja_realisasi_kinerja_tw.id_evaluasi_renja=t_evaluasi_renja.id", "inner")
									->order_by("t_evaluasi_renja.id")
									->get()
									->result();

		$temp = array();
		foreach ($realisasi as $row) {
			$temp[$row->id_evaluasi_renja][$row->triwulan] = $row;
		}
		$data['realisasi_evaluasi_renja'] = $temp;

		return $data;
	}

	private function approved_evaluasi($id){
		$this->db->where("t_evaluasi_renja.id", $id);
		$return = $this->db->update("t_evaluasi_renja", array('status' => 5));
		return $return;
	}

	private function not_approved_evaluasi($id, $ket){
		$this->db->trans_strict(FALSE);
		$this->db->trans_start();

		$this->db->where("t_evaluasi_renja.id", $id);
		$return = $this->db->update("t_evaluasi_renja", array('status' => 4));

		$result = $this->db->insert("t_evaluasi_renja_revisi", array('id_evaluasi_renja' => $id, 'ket' => $ket, 'cru_by' => $this->session->userdata('username'), 'cru_date' => date("Y-m-d H:i:s")));

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function update_status($veri, $id, $ket)
	{
		if ($veri==1) {
			return $this->approved_evaluasi($id);
		}elseif ($veri==0) {
			return $this->not_approved_evaluasi($id, $ket);
		}
	}

	public function get_revisi_evaluasi_renja($id)
	{
		return $this->db->from("t_evaluasi_renja_revisi")
						->where("id_evaluasi_renja", $id)
						->get()
						->result();
	}

  public function get_evaluasi_renja_urusan_bidang($tahun, $tw, $id_skpd)
  {
    $data['kode_urusan'] = $this->db->query("SELECT t_evaluasi_renja_prog_keg.*, m_urusan.Nm_Urusan AS urusan FROM t_evaluasi_renja_prog_keg INNER JOIN t_evaluasi_renja ON t_evaluasi_renja.id_evaluasi_renja_prog_keg=t_evaluasi_renja_prog_keg.id INNER JOIN m_urusan ON m_urusan.Kd_Urusan=t_evaluasi_renja_prog_keg.kd_urusan WHERE t_evaluasi_renja.status=5 AND tahun=? AND triwulan=? AND t_evaluasi_renja_prog_keg.id_skpd=? GROUP BY kd_urusan;", array($tahun, $tw, $id_skpd))
                                    ->result();

    $temp = array();
    $kode_bidang = $this->db->query("SELECT t_evaluasi_renja_prog_keg.*, m_bidang.Nm_Bidang AS bidang FROM t_evaluasi_renja_prog_keg INNER JOIN t_evaluasi_renja ON t_evaluasi_renja.id_evaluasi_renja_prog_keg=t_evaluasi_renja_prog_keg.id INNER JOIN m_bidang ON m_bidang.Kd_Urusan=t_evaluasi_renja_prog_keg.kd_urusan AND m_bidang.Kd_Bidang=t_evaluasi_renja_prog_keg.kd_bidang WHERE t_evaluasi_renja.status=5 AND tahun=? AND triwulan=? AND t_evaluasi_renja_prog_keg.id_skpd=? GROUP BY kd_urusan, kd_bidang", array($tahun, $tw, $id_skpd))
                            ->result();
    foreach ($kode_bidang as $key => $value) {
      $temp[$value->kd_urusan][] = $value;
    }
    $data['kode_bidang'] = $temp;

    return $data;
  }

	public function cetak_evaluasi_each_triwulan($tahun, $id_skpd, $triwulan, $urusan, $bidang)
	{
    $where = array(
						't_evaluasi_renja_prog_keg.tahun' => $tahun,
						't_evaluasi_renja_prog_keg.id_skpd' => $id_skpd,
            't_evaluasi_renja_prog_keg.triwulan' => $triwulan,
            't_evaluasi_renja_prog_keg.kd_urusan' => $urusan,
            't_evaluasi_renja_prog_keg.kd_bidang' => $bidang,
            't_evaluasi_renja.status' => 5
					);

		$data['evaluasi_renja_prog_keg'] = $this->db->where($where)
                                                ->select("t_evaluasi_renja_prog_keg.*")
                              									->from("t_evaluasi_renja_prog_keg")
                                                ->join("t_evaluasi_renja", "t_evaluasi_renja.id_evaluasi_renja_prog_keg=t_evaluasi_renja_prog_keg.id", "inner")
                                                ->order_by("kd_urusan, kd_bidang, kd_program, kd_kegiatan, is_prog_or_keg")
                                                ->group_by("t_evaluasi_renja_prog_keg.id")
                                                ->get()
                              									->result();

		$indikator = $this->db->where($where)
        									->select("t_evaluasi_renja.*")
        									->select("m_lov.nama_value AS satuan", false)
                          ->from("t_evaluasi_renja")
        									->join("t_evaluasi_renja_prog_keg", "t_evaluasi_renja.id_evaluasi_renja_prog_keg=t_evaluasi_renja_prog_keg.id", "inner")
        									->join("m_lov", "t_evaluasi_renja.satuan=m_lov.kode_value AND nama_app='satuan'", "left")
        									->order_by("t_evaluasi_renja_prog_keg.id")
        									->get()
        									->result();

		$temp = array();
		$jumlah = array();
		foreach ($indikator as $row) {
			$temp[$row->id_evaluasi_renja_prog_keg][] = $row;

			if (empty($jumlah[$row->id_evaluasi_renja_prog_keg])) {
				$jumlah[$row->id_evaluasi_renja_prog_keg]=1;
			}else{
				$jumlah[$row->id_evaluasi_renja_prog_keg]++;
			}
		}
		$data['evaluasi_renja'] = $temp;
		$data['jumlah_evaluasi_renja'] = $jumlah;

    $realisasi = $this->db->where($where)
                          ->select("t_evaluasi_renja_realisasi_kinerja_tw_prog_keg.*")
        									->from("t_evaluasi_renja_prog_keg")
                          ->join("t_evaluasi_renja", "t_evaluasi_renja.id_evaluasi_renja_prog_keg=t_evaluasi_renja_prog_keg.id", "inner")
                          ->join("t_evaluasi_renja_realisasi_kinerja_tw_prog_keg", "t_evaluasi_renja_realisasi_kinerja_tw_prog_keg.id_evaluasi_renja_prog_keg=t_evaluasi_renja_prog_keg.id", "inner")
                          ->order_by("t_evaluasi_renja_prog_keg.id")
                          ->group_by("t_evaluasi_renja_prog_keg.id, t_evaluasi_renja_realisasi_kinerja_tw_prog_keg.id")
                          ->get()
        									->result();

		$temp = array();
		foreach ($realisasi as $row) {
			$temp[$row->id_evaluasi_renja_prog_keg][$row->triwulan] = $row;
		}
		$data['realisasi_evaluasi_renja_prog_keg'] = $temp;

		$realisasi = $this->db->where($where)
        									->select("t_evaluasi_renja_realisasi_kinerja_tw.*")
                          ->from("t_evaluasi_renja")
        									->join("t_evaluasi_renja_prog_keg", "t_evaluasi_renja.id_evaluasi_renja_prog_keg=t_evaluasi_renja_prog_keg.id", "inner")
                          ->join("t_evaluasi_renja_realisasi_kinerja_tw", "t_evaluasi_renja_realisasi_kinerja_tw.id_evaluasi_renja=t_evaluasi_renja.id", "inner")
        									->order_by("t_evaluasi_renja.id")
        									->get()
        									->result();

		$temp = array();
		foreach ($realisasi as $row) {
			$temp[$row->id_evaluasi_renja][$row->triwulan] = $row;
		}
		$data['realisasi_evaluasi_renja'] = $temp;

		return $data;
	}

	public function get_preview($id_skpd, $tahun)
	{
    return $this->db->select("t_evaluasi_renja_prog_keg.triwulan, t_evaluasi_renja_prog_keg.tahun")
                    ->from("t_evaluasi_renja")
  									->join("t_evaluasi_renja_prog_keg", "t_evaluasi_renja.id_evaluasi_renja_prog_keg=t_evaluasi_renja_prog_keg.id", "inner")
                    ->where("t_evaluasi_renja_prog_keg.tahun", $tahun)
        						->where("t_evaluasi_renja_prog_keg.id_skpd", $id_skpd)
        						->where("t_evaluasi_renja.status", 5)
  									->order_by("t_evaluasi_renja_prog_keg.id")
                    ->group_by("t_evaluasi_renja_prog_keg.triwulan, t_evaluasi_renja_prog_keg.tahun")
  									->get()
  									->result();
	}

  public function get_all_skpd_in_evaluasi_renja()
  {
    return $this->db->query("SELECT m_skpd.*
                              FROM
                                t_evaluasi_renja
                              INNER JOIN
                                t_renja_indikator_prog_keg ON t_evaluasi_renja.id_indikator_prog_keg=t_renja_indikator_prog_keg.id
                              INNER JOIN
                                t_renja_prog_keg ON t_renja_indikator_prog_keg.id_prog_keg=t_renja_prog_keg.id
                              INNER JOIN
                                m_skpd ON t_renja_prog_keg.id_skpd=m_skpd.id_skpd
                              GROUP BY id_skpd")
                    ->result();
  }

  public function hitung_capaian_lap($var1, $var2){
    if (empty($var2)) {
      return '-';
    }else{
      $temp = $var1/$var2;
      $temp = round($temp, 1);
      return $temp;
    }
  }

  public function predikat_capaian_lap($var1, $var2){
    if (empty($var2)) {
      return '-';
    }else{
      $temp = $var1/$var2;

      $predikat = '-';
      if ($temp <= 50) {
        $predikat = "SR";
      }elseif ($temp > 50 && $temp <= 65) {
        $predikat = "R";
      }elseif ($temp > 65 && $temp <= 75) {
        $predikat = "S";
      }elseif ($temp > 75 && $temp <= 90) {
        $predikat = "T";
      }elseif ($temp > 90) {
        $predikat = "ST";
      }
      return $predikat;
    }
  }
}
?>
