<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rpjmd extends CI_Controller
{
	var $CI = NULL;
	public function __construct(){
		$this->CI =& get_instance(); 
        parent::__construct();        
        $this->load->model(array('m_rpjmd_trx', 'm_renstra_trx', 'm_skpd', 'm_template_cetak', 'm_lov', 'm_urusan', 'm_bidang', 'm_program', 'm_kegiatan'));
	}

	function index(){
		$this->rpjmd();
	}

	function rpjmd(){
		$this->auth->restrict();	

		$data['rpjmd'] = $this->m_rpjmd_trx->get_all_rpjmd();		
		$this->template->load('template','rpjmd/view_all', $data);		
	}

	function cru_rpjmd($id=NULL){
		$this->auth->restrict();
		$data['rpjmd'] = NULL;
		$rpjmd = $this->m_rpjmd_trx->get_one_rpjmd($id, TRUE);
		if (!empty($rpjmd)) {
			$data['rpjmd'] = $rpjmd;
			$data['rpjmd_misi'] = $this->m_rpjmd_trx->get_all_rpjmd_misi($rpjmd->id, TRUE);			
		}
		$this->template->load('template','rpjmd/cru_rpjmd', $data);
	}

	function save(){
		$id = $this->input->post('id_rpjmd');
		$misi = $this->input->post('misi');
		$id_misi = $this->input->post('id_misi');
		$tujuan = $this->input->post('tujuan');
		$id_tujuan = $this->input->post('id_tujuan');
		
		$data = $this->input->post();
		$clean = array('id_rpjmd','misi', 'tujuan', 'id_tujuan', 'id_misi');		
		$data = $this->global_function->clean_array($data, $clean);

		if (!empty($id)) {
			$result = $this->m_rpjmd_trx->edit_rpjmd($data, $misi, $tujuan, $id_misi, $id_tujuan, $id);
		}else{
			$result = $this->m_rpjmd_trx->add_rpjmd($data, $misi, $tujuan);
		}

		if ($result) {
			$this->session->set_userdata('msg_typ','ok');
			$this->session->set_userdata('msg', 'RPJMD berhasil dibuat.');
			redirect('rpjmd');
		}else{
			$this->session->set_userdata('msg_typ','err');
			$this->session->set_userdata('msg', 'ERROR! RPJMD gagal dibuat, mohon menghubungi administrator.');
			redirect('rpjmd');
		}
	}

	function delete_rpjmd(){
		$id = $this->input->post('id');
		$result = $this->m_rpjmd_trx->delete_rpjmd($id);
		if ($result) {
			$msg = array('success' => '1', 'msg' => 'RPJMD berhasil dihapus.');
			echo json_encode($msg);			
		}else{
			$msg = array('success' => '0', 'msg' => 'ERROR! RPJMD gagal dihapus, mohon menghubungi administrator.');
			echo json_encode($msg);			
		}
	}

	function det_rpjmd($id=NULL){
		if (empty($id)) {
			redirect('rpjmd');
		}
		$this->auth->restrict();		
		$rpjmd = $this->m_rpjmd_trx->get_one_rpjmd($id, TRUE);
		$data['rpjmd'] = $rpjmd;
		$data['rpjmd_misi'] = $this->m_rpjmd_trx->get_all_rpjmd_misi($rpjmd->id, TRUE);
		$data['rpjmd_tujuan'] = $this->m_rpjmd_trx->get_all_rpjmd_tujuan($rpjmd->id);
		$this->template->load('template','rpjmd/view', $data);
	}

	function get_sasaran(){		
		$id_rpjmd = $this->input->post('id_rpjmd');
		$id_tujuan = $this->input->post('id_tujuan');

		$data['sasaran'] = $this->m_rpjmd_trx->get_all_sasaran($id_rpjmd, $id_tujuan);
		$this->load->view("rpjmd/view_sasaran", $data);
	}

	function cru_sasaran(){
		$id_rpjmd = $this->input->post('id_rpjmd');
		$id_tujuan = $this->input->post('id_tujuan');
		$id_sasaran = $this->input->post('id_sasaran');
		
		if (!empty($id_sasaran)) {			
			$result = $this->m_rpjmd_trx->get_one_sasaran($id_rpjmd, $id_tujuan, $id_sasaran);			
			if (empty($result)) {
				echo '<div style="width: 400px;">ERROR! Data sasaran tidak ditemukan.</div>';
				return FALSE;
			}
			$data['sasaran'] = $result;
			$data['kebijakan_sasaran'] = $this->m_rpjmd_trx->get_kebijakan_sasaran($id_sasaran, FALSE);
		}

		$data['id_rpjmd'] = $id_rpjmd;
		$data['tujuan'] = $this->m_rpjmd_trx->get_one_rpjmd_tujuan($id_rpjmd, $id_tujuan);

		$this->load->view("rpjmd/cru_sasaran", $data);
	}

	function save_sasaran(){
		$id = $this->input->post('id_sasaran');		

		$data = $this->input->post();
		$id_kebijakan = $this->input->post("id_kebijakan");
		$kebijakan = $this->input->post("kebijakan");
		$clean = array('id_sasaran', 'kebijakan', 'id_kebijakan');		
		$data = $this->global_function->clean_array($data, $clean);

		if (!empty($id)) {
			$result = $this->m_rpjmd_trx->edit_sasaran($data, $id, $kebijakan, $id_kebijakan);
		}else{
			$result = $this->m_rpjmd_trx->add_sasaran($data, $kebijakan);
		}

		if ($result) {
			$msg = array('success' => '1', 'msg' => 'Sasaran berhasil dibuat.');
			echo json_encode($msg);			
		}else{
			$msg = array('success' => '0', 'msg' => 'ERROR! Sasaran gagal dibuat, mohon menghubungi administrator.');
			echo json_encode($msg);			
		}
	}

	function delete_sasaran(){
		$id = $this->input->post('id_sasaran');
		$result = $this->m_rpjmd_trx->delete_sasaran($id);
		if ($result) {
			$msg = array('success' => '1', 'msg' => 'Sasaran berhasil dibuat.');
			echo json_encode($msg);			
		}else{
			$msg = array('success' => '0', 'msg' => 'ERROR! Sasaran gagal dibuat, mohon menghubungi administrator.');
			echo json_encode($msg);			
		}
	}
	
	function get_indikator(){		
		$id_rpjmd = $this->input->post('id_rpjmd');		
		$id_sasaran = $this->input->post('id_sasaran');

		$data['id_rpjmd'] = $id_rpjmd;
		$data['id_sasaran'] = $id_sasaran;
		$data['indikator'] = $this->m_rpjmd_trx->get_all_indikator($id_rpjmd, $id_sasaran, TRUE);
		$this->load->view("rpjmd/view_indikator", $data);
	}

	function cru_indikator(){
		$id_rpjmd = $this->input->post('id_rpjmd');		
		$id_sasaran = $this->input->post('id_sasaran');
		$id_indikator = $this->input->post('id_indikator');		

		$satuan_edit = NULL;		
		if (!empty($id_indikator)) {			
			$result = $this->m_rpjmd_trx->get_one_indikator($id_rpjmd, $id_sasaran, $id_indikator);			
			if (empty($result)) {
				echo '<div style="width: 400px;">ERROR! Data sasaran tidak ditemukan.</div>';
				return FALSE;
			}
			$data['indikator'] = $result;
			$satuan_edit = $result->satuan_target;			
		}

		$data['id_rpjmd'] = $id_rpjmd;
		$data['id_sasaran'] = $id_sasaran;
		$data['tujuan_n_sasaran'] = $this->m_rpjmd_trx->get_info_tujuan_n_sasaran_n_indikator(NULL, $id_sasaran);
		
		$satuan = array("" => "~~ Pilih Satuan ~~");
		foreach ($this->m_lov->get_list_lov(1) as $row) {
			$satuan[$row->kode_value]=$row->nama_value;
		}				

		$data['satuan'] = form_dropdown('satuan_target', $satuan, $satuan_edit, 'class="common" id="satuan_target"');		
		$this->load->view("rpjmd/cru_indikator", $data);
	}

	function save_indikator(){		
		$id = $this->input->post('id_indikator');		

		$data = $this->input->post();
		$clean = array('id_indikator');		
		$data = $this->global_function->clean_array($data, $clean);

		if (!empty($id)) {
			$result = $this->m_rpjmd_trx->edit_indikator($data, $id);
		}else{
			$result = $this->m_rpjmd_trx->add_indikator($data);
		}

		if ($result) {
			$msg = array('success' => '1', 'msg' => 'Indikator berhasil dibuat.');
			echo json_encode($msg);			
		}else{
			$msg = array('success' => '0', 'msg' => 'ERROR! Indikator gagal dibuat, mohon menghubungi administrator.');
			echo json_encode($msg);			
		}
	}
	
	function delete_indikator(){
		$id = $this->input->post('id_indikator');
		$result = $this->m_rpjmd_trx->delete_indikator($id);
		if ($result) {
			$msg = array('success' => '1', 'msg' => 'Indikator berhasil dihapus.');
			echo json_encode($msg);			
		}else{
			$msg = array('success' => '0', 'msg' => 'ERROR! Indikator gagal dihapus, mohon menghubungi administrator.');
			echo json_encode($msg);			
		}
	}
	
	function get_program(){		
		$id_rpjmd = $this->input->post('id_rpjmd');		
		$id_sasaran = $this->input->post('id_sasaran');
		$id_indikator = $this->input->post('id_indikator');

		$data['id_rpjmd'] = $id_rpjmd;
		$data['id_sasaran'] = $id_sasaran;
		$data['id_indikator'] = $id_indikator;
		$data['program'] = $this->m_rpjmd_trx->get_all_program($id_rpjmd, $id_sasaran, $id_indikator, TRUE);
		$this->load->view("rpjmd/view_program", $data);
	}

	function pilih_program(){		
		$id_rpjmd = $this->input->post('id_rpjmd');		
		$id_sasaran = $this->input->post('id_sasaran');
		$id_indikator = $this->input->post('id_indikator');

		$data['id_rpjmd'] = $id_rpjmd;
		$data['id_sasaran'] = $id_sasaran;
		$data['id_indikator'] = $id_indikator;

		$this->load->view("rpjmd/pilih_program", $data);
	}
	
	function get_pilih_program(){
		$search = $this->input->post("search");
		$start = $this->input->post("start");
		$length = $this->input->post("length");
		$order = $this->input->post("order");
		$id_rpjmd = $this->input->post("id_rpjmd");
		$id_sasaran = $this->input->post("id_sasaran");
		$id_indikator = $this->input->post("id_indikator");

		$order_arr = array('Nm_Bidang','','','','nama_prog_or_keg','nama_skpd', 'Nm_Bidang');
		$program = $this->m_rpjmd_trx->get_all_pilih_program($search, $start, $length, $order["0"], $order_arr, $id_rpjmd, $id_sasaran, $id_indikator);
		$alldata = $this->m_rpjmd_trx->count_all_pilih_program($search, $start, $length, $order["0"], $order_arr, $id_rpjmd, $id_sasaran, $id_indikator);
		
		$data = array();
		$no=0;
		foreach ($program as $row) {
			$no++;			

			$data[] = array(
							$no, 							
							$row->kd_urusan.". ".$row->kd_bidang.". ".$row->kd_program,
							$row->nama_prog_or_keg,
							$row->Nm_Bidang,
							$row->nama_skpd,
							'<a href="javascript:void(0)" onclick="pilih_program(\''. $id_rpjmd .'\',\''. $id_sasaran .'\',\''. $id_indikator .'\',\''. $row->id .'\')" class="icon-ok" title="Pilih Program"/>'
							);
		}
		$json = array("recordsTotal"=> $alldata, "recordsFiltered"=> $alldata, 'data' => $data);
		echo json_encode($json);
	}

	function do_pilih_program(){		
		$data = $this->input->post();
		$result = $this->m_rpjmd_trx->add_program($data);		

		if ($result) {
			$msg = array('success' => '1', 'msg' => 'Program berhasil ditambahkan.');
			echo json_encode($msg);			
		}else{
			$msg = array('success' => '0', 'msg' => 'ERROR! Program gagal ditambahkan, mohon menghubungi administrator.');
			echo json_encode($msg);			
		}		
	}

	function delete_program(){
		$id = $this->input->post('id_program');
		$result = $this->m_rpjmd_trx->delete_program($id);
		if ($result) {
			$msg = array('success' => '1', 'msg' => 'Indikator berhasil dihapus.');
			echo json_encode($msg);			
		}else{
			$msg = array('success' => '0', 'msg' => 'ERROR! Indikator gagal dihapus, mohon menghubungi administrator.');
			echo json_encode($msg);			
		}
	}

	function preview_program_rpjmd(){
		$id = $this->input->post("id");

		$result = $this->m_rpjmd_trx->preview_program_rpjmd($id);
		if (!empty($result)) {
			$data['rpjmd'] = $result;
			$data['indikator_program'] = $this->m_renstra_trx->get_indikator_prog_keg($result->id);
			$this->load->view('rpjmd/preview', $data);	
		}else{
			echo "Data tidak ditemukan . . .";
		}
	}

	private function cetak_func($cetak=FALSE){
		if (!$cetak) {
			$temp['class_table']='class="table-common"';
		}else{
			$temp['class_table']='class="border"';
		}
		$temp['misi'] = $this->m_rpjmd_trx->get_misi_rpjmd_4_cetak_final();
		$temp['bidang_urusans'] = $this->m_rpjmd_trx->get_all_bidang_urusan_4_cetak_final();
		$result = $this->load->view("rpjmd/cetak/cetak_bidang_urusan", $temp, TRUE);
		return $result;
	}

	function cetak(){
		$this->auth->restrict();

		$data['cetak'] = $this->cetak_func();

		$this->template->load('template','rpjmd/cetak/view', $data);
	}

	function do_cetak(){
		ini_set('memory_limit', '-1');
		
		$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://';
		$header = $this->m_template_cetak->get_value("GAMBAR");		
		$data['logo'] = str_replace("src=\"","height=\"90px\" src=\"".$protocol.$_SERVER['HTTP_HOST'],$header);
		$data['header'] = $this->m_template_cetak->get_value("HEADER");

		$data['cetak'] = $this->cetak_func(TRUE);
		$html = $this->template->load('template_cetak', 'rpjmd/cetak/cetak_view', $data, true);
			
        $filename='RPJMD '. date("d-m-Y_H-i-s") .'.pdf';        
	    pdf_create($html, $filename, "A4", "Landscape", FALSE);
	}

	##########
	# Revisi #
	##########
	function view_revisi(){
		$data['action'] = 'pengajuan';
		$data['revisi'] = $this->m_rpjmd_trx->get_revisi_pengajuan();
		$this->template->load('template','rpjmd/revisi/view', $data);
	}

	function do_p_revisi(){
		$skpd_setuju = $this->input->post("skpd_setuju");
		$skpd_tdk_setuju = $this->input->post("skpd_id");
		$action = $this->input->post("action");

		/*if ($action =="skpd") {
			$result = $this->m_rpjmd_trx->proses_revisi($skpd_setuju, $skpd_tdk_setuju, FALSE);
		}elseif ($action =="pengajuan") {
			$result = $this->m_rpjmd_trx->proses_revisi($skpd_setuju, $skpd_tdk_setuju);
		}*/
		$result = $this->m_rpjmd_trx->proses_revisi($skpd_setuju, $skpd_tdk_setuju);

		if ($result) {
			$msg = array('success' => '1', 'msg' => 'Pengajuan Revisi berhasil diproses.');
			echo json_encode($msg);
		}
	}

	function view_skpd_revisi(){
		$data['action'] = 'skpd';
		$data['revisi'] = $this->m_rpjmd_trx->get_revisi_skpd();
		$this->template->load('template','rpjmd/revisi/view_skpd', $data);
	}

	function view_p_revisi(){
		$this->auth->restrict_ajax_login();
		$data['id_skpd'] = $this->input->post('id');		
		
		$msg = $this->load->view('rpjmd/pengajuan_revisi', $data, TRUE);			
		echo json_encode(array('msg' => $msg));
	}

	function p_revisi(){
		$this->auth->restrict_ajax_login();

		$id_skpd = $this->session->userdata('id_skpd');
		$ket = $this->input->post('ket');
		$result = $this->m_rpjmd_trx->simpan_revisi($id_skpd, $ket);
		echo json_encode(array('success' => '1', 'msg' => 'Pengajuan Revisi Berhasil di kirim.'));
	}
}