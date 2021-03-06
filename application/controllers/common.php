<?php
/**
 *
 * Class ini digunakan sebagai common gateway ke internal data
 *
 *
 */
class Common extends CI_Controller
{
    var $CI = NULL;
    public function __construct()
    {
        $this->CI =& get_instance();
        parent::__construct();        
        $this->load->model(array('m_bidang', 'm_urusan', 'm_kegiatan', 'm_program', 'm_skpd', 'm_kecamatan', 'm_desa', 'm_groups','m_jenis_pendapatan','m_asal_pendapatan'));
    }
    
    function autocomplete_kdurusan(){
        $req = $this->input->post('term');      
        $result = $this->m_urusan->get_value_autocomplete_kd_urusan($req);      
        echo json_encode($result);
    }

    function autocomplete_kdbidang(){       
        $kd_urusan = $this->input->post('kd_urusan');       
        $req = $this->input->post('term');      
        $result = $this->m_bidang->get_value_autocomplete_kd_bidang($req, $kd_urusan);      
        echo json_encode($result);
    }

    function autocomplete_kdprog(){     
        $kd_urusan = $this->input->post('kd_urusan');       
        $kd_bidang = $this->input->post('kd_bidang');       
        $req = $this->input->post('term');      
        $result = $this->m_program->get_value_autocomplete_kd_prog($req, $kd_urusan, $kd_bidang);
        echo json_encode($result);
    }

    function autocomplete_keg(){        
        $kd_urusan  = $this->input->post('kd_urusan');      
        $kd_bidang  = $this->input->post('kd_bidang');      
        $kd_prog    = $this->input->post('kd_prog');        
        $req = $this->input->post('term');      
        $result = $this->m_kegiatan->get_value_autocomplete_kd_keg($req, $kd_urusan, $kd_bidang, $kd_prog);
        echo json_encode($result);
    }

    function autocomplete_skpd(){                
        $req = $this->input->post('term');      
        $result = $this->m_skpd->get_skpd_autocomplete($req);
        echo json_encode($result);
    }

    function autocomplete_kec(){
        $req = $this->input->post('term');      
        $result = $this->m_kecamatan->get_kec_autocomplete($req);
        echo json_encode($result);
    }

    function autocomplete_desa(){
        $req = $this->input->post('term');      
        $id_kec = $this->input->post('id_kec');      
        $result = $this->m_desa->get_desa_autocomplete($req, $id_kec);
        echo json_encode($result);
    }
	
	function autocomplete_groups(){
        $req = $this->input->post('term');      
        $result = $this->m_groups->get_groups_autocomplete($req);
        echo json_encode($result);
    }

    function cmb_bulan(){
        $id_bulan = array("" => "");
        foreach ($this->m_bulan->get_bulan() as $row) {
            $id_bulan[$row->id] = $row->id .". ". $row->nama;
        }        
        $cmb = form_dropdown('id_bulan', $id_bulan, NULL, 'data-placeholder="Pilih Bulan" class="common chosen-select" id="id_bulan"');
        echo $cmb;
    }

    function cmb_skpd(){
        $id_skpd = array("" => "");
        foreach ($this->m_skpd->get_skpd_chosen() as $row) {
            $id_skpd[$row->id] = $row->id .". ". $row->label;
        }        
        $cmb = form_dropdown('id_skpd', $id_skpd, NULL,'data-placeholder="Pilih SKPD" class="common chosen-select" id="id_skpd"');
        echo $cmb;
    }

    function cmb_urusan(){
        $kd_urusan = array("" => "");
        foreach ($this->m_urusan->get_urusan() as $row) {
            $kd_urusan[$row->id] = $row->id .". ". $row->nama;
        }        
        $cmb = form_dropdown('kd_urusan', $kd_urusan, NULL, 'data-placeholder="Pilih Kode Urusan" class="common chosen-select" id="kd_urusan"');
        echo $cmb;
    }
	
	function cmb_bidang(){
        $kd_urusan = $this->input->post('kd_urusan');
        $kd_bidang = array("" => "");
        foreach ($this->m_bidang->get_bidang($kd_urusan) as $row) {
            $kd_bidang[$row->id] = $row->id .". ". $row->nama;
        }        
        $cmb = form_dropdown('kd_bidang', $kd_bidang, NULL, 'data-placeholder="Pilih Kode Bidang" class="common chosen-select" id="kd_bidang"');
        echo $cmb;
    }

    function cmb_program(){
        $kd_urusan = $this->input->post('kd_urusan');
        $kd_bidang = $this->input->post('kd_bidang');       

        $kd_program = array("" => "");
        foreach ($this->m_program->get_prog($kd_urusan, $kd_bidang) as $row) {
            $kd_program[$row->id] = $row->id .". ". $row->nama;
        }
        $cmb = form_dropdown('kd_program', $kd_program, NULL, 'data-placeholder="Pilih Kode Program" class="common chosen-select" id="kd_program"');
        echo $cmb;
    }

    function cmb_kegiatan(){
        $kd_urusan = $this->input->post('kd_urusan');
        $kd_bidang = $this->input->post('kd_bidang');
        $kd_program = $this->input->post('kd_program');       

        $kd_kegiatan = array("" => "");
        foreach ($this->m_kegiatan->get_keg($kd_urusan, $kd_bidang, $kd_program) as $row) {
            $kd_kegiatan[$row->id] = $row->id .". ". $row->nama;
        }
        $cmb = form_dropdown('kd_kegiatan', $kd_kegiatan, NULL, 'data-placeholder="Pilih Kode Kegiatan" class="common chosen-select" id="kd_kegiatan"');
        echo $cmb;
    }
	
	function cmb_groups(){
		$id_groups = $this->input->post('id_groups');
		$id_groups = array("" => "");
        foreach ($this->m_groups->get_group($id_groups) as $row) {
            $id_groups[$row->id] = $row->id .". ". $row->nama;
        }        
        $cmb = form_dropdown('id_groups', $id_groups, NULL, 'data-placeholder="Pilih Jenis Grup" class="common chosen-select" id="id_groups"');
        echo $cmb;
		
		}

    function autocomplete_jenispendapatan(){
        $req = $this->input->post('term');      
        $result = $this->m_jenis_pendapatan->get_value_autocomplete_jenis_pendapatan($req);      
        echo json_encode($result);
    }
    function autocomplete_asalpendapatan(){       
        $kd_jenis = $this->input->post('kd_jenis');       
        $req = $this->input->post('term');      
        $result = $this->m_asal_pendapatan->get_value_autocomplete_asal_pendapatan($req, $kd_jenis);      
        echo json_encode($result);
    }
}

?>