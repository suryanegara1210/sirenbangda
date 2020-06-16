<script type="text/javascript">
	$('#nominal').autoNumeric(numOptions);
	prepare_chosen();
	$(document).on("change", "#kd_urusan", function () {		
		$.ajax({
			type: "POST",
			url: '<?php echo site_url("common/cmb_bidang"); ?>',
			data: {kd_urusan: $(this).val()},
			success: function(msg){
				$("#cmb-bidang").html(msg);				
				$("#kd_program").val("");				
				$("#nama_prog_or_keg").val("");		
        		$("#kd_program").trigger("chosen:updated");
				prepare_chosen();
			}
		});
	});

	$(document).on("change", "#kd_bidang", function () {		
		$.ajax({
			type: "POST",
			url: '<?php echo site_url("common/cmb_program"); ?>',
			data: {kd_urusan:$("#kd_urusan").val(), kd_bidang: $(this).val()},
			success: function(msg){
				$("#cmb-program").html(msg);
				prepare_chosen();
			}
		});
	});

	$(document).on("change", "#kd_program", function () {		
		var str = $(this).find('option:selected').text();		
		var nama_program = str.substring(str.indexOf(".")+2);		
		$("#nama_prog_or_keg").val(nama_program);		
	});

	//agar validation tetap aktif untuk chosen dropdown	
	$('form#program').validate({
		rules: {
			kd_urusan : "required",
			kd_bidang : "required",
			kd_program : "required"			
		},
		ignore: ":hidden:not(select)"			
	});

	$("#simpan").click(function(){
		$('#indikator_frame_program .indikator_val').each(function () {
		    $(this).rules('add', {
		        required: true
		    });
		});

		$('#indikator_frame_program .target').each(function () {
		    $(this).rules('add', {
		        required:true,
				number:true
		    });
		});		
		
	    var valid = $("form#program").valid();
	    if (valid) {
	    	$.blockUI({
				css: window._css,
				overlayCSS: window._ovcss
			});
	    	
	    	$.ajax({
				type: "POST",
				url: $("form#program").attr("action"),
				data: $("form#program").serialize(),
				dataType: "json",
				success: function(msg){
					if (msg.success==1) {														
						$.blockUI({
							message: msg.msg,
							timeout: 2000,
							css: window._css,
							overlayCSS: window._ovcss
						});							
						$.facebox.close();
						location.reload();						
						
					};						
				}
			});
	    };
	});

	$("#tambah_indikator_program").click(function(){		
		key = $("#indikator_frame_program").attr("key");
		key++;
		$("#indikator_frame_program").attr("key", key);

		var name = "indikator_kinerja["+ key +"]";
		var target = "target["+ key +"]";
		var target_thndpn = "target_thndpn["+ key +"]";
		var satuan_target = "satuan_target["+ key +"]";

		$("#indikator_box_program texarea").attr("name", name);
		$("#indikator_box_program input#target").attr("name", target);
		$("#indikator_box_program input#target_thndpn").attr("name", target_thndpn);
		$("#indikator_box_program select#satuan_target").attr("name", satuan_target);
		$("#indikator_frame_program").append($("#indikator_box_program").html());
	});

	$(document).on("click", ".hapus_indikator_program", function(){
		$(this).parent().parent().remove();
	});
</script>

<div style="width: 900px">
	<header>
		<h3 style="padding:20px">
	<?php
		if (!empty($program)){
		    echo "Edit Data Program";
		} else{
		    echo "Input Data Program";
		}
	?>
	</h3>
	</header>
	<div class="module_content">
    <?php
	if($status == "3"){ ?>
    <table class="table-common" style="width: 99.8%">
    	<thead>
        <tr align="center"><th colspan="2">Revisi</th></tr>
        <tr>
        	<th align="center">No</th>
            <th align="left">Keterangan</th>
        </tr>
        </thead>
        <tbody>
        <?php
		$i=0;
		foreach ($revisi as $row) {
		$i++;
		?>
        <tr>
        	<td align="center"><?php echo $i;?></td>
            <td align="left"><?php echo $row->ket;?></td>
        </tr>
        <?php
		}
		?>
        </tbody>
    </table>
	<?php
    } 
	?>
    <br />
		<form action="<?php echo site_url('renja/save_program_renja');?>" method="POST" name="program" id="program" accept-charset="UTF-8" enctype="multipart/form-data" >
        <input type="hidden" name="id_skpd" value="<?php echo $this->session->userdata("id_skpd"); ?>" />
        <input type="hidden" name="tahun" value="<?php echo $this->m_settings->get_tahun_anggaran(); ?>" />
			<input type="hidden" name="id_program" value="<?php if(!empty($program->id)){echo $program->id;} ?>" />
			<input type="hidden" id="nama_prog_or_keg" name="nama_prog_or_keg" value="<?php echo (!empty($program->nama_prog_or_keg))?$program->nama_prog_or_keg:''; ?>" />
			
			<table class="fcari" width="99.8%">
				<tbody>
					<tr>
						<td width="15%">SKPD</td>
						<td width="85%"><?php echo $skpd->nama_skpd; ?></td>
					</tr>
					<tr>
						<td>Urusan</td>
						<td>
							<?php echo $kd_urusan; ?>
		    			</td>
					</tr>
					<tr>
						<td>Bidang Urusan</td>
						<td id="cmb-bidang">
							<?php echo $kd_bidang; ?>
						</td>
					</tr>
					<tr>
						<td>Program</td>
						<td id="cmb-program">
							<?php echo $kd_program; ?>
						</td>
					</tr>								
					<tr>
					  <td colspan="2">
                      <?php
					  		$ta=$this->m_settings->get_tahun_anggaran();
							$tahun_n1=0;
							$tahun_n1= $ta+1; 
					  ?>
                      <div>
                      <table class="table-common" width="100%">
                      <tr>
                      	<th align="center" width="50%">Tahun <?php echo $ta;?></th>
                        <th align="center" width="50%">Tahun <?php echo $tahun_n1;?></th>
                      </tr>
                      </table>
                      </div>
                      </td>
				  </tr>
					<tr>
						<td>Indikator Kinerja <a id="tambah_indikator_program" class="icon-plus-sign" href="javascript:void(0)"></a></td>
						<td id="indikator_frame_program" key="<?php echo (!empty($indikator_program))?$indikator_program->num_rows():'1'; ?>">
							<?php
								if (!empty($indikator_program)) {
									$i=0;
									foreach ($indikator_program->result() as $row) {
										$i++;
							?>
							<input type="hidden" name="id_indikator_program[<?php echo $i; ?>]" value="<?php echo $row->id; ?>">
							<div style="margin-top:5px">
							  <div style="width:100%">
									<textarea class="common indikator_val" name="indikator_kinerja[<?php echo $i; ?>]" style="width:95%"><?php if(!empty($row->indikator)){echo $row->indikator;} ?></textarea>
                                    
							<?php
								if ($i != 1) {
							?>
								<a class="icon-remove hapus_indikator_program" href="javascript:void(0)" style="vertical-align: top;"></a>
							<?php
								}
							?>		
								</div>
								<div style="width: 100%;">
									<table class="table-common" width="100%">
										<tr style="width:100%">
											<td>Satuan</td>
											<td colspan="3"><?php echo form_dropdown('satuan_target['. $i .']', $satuan, $row->satuan_target, 'class="common indikator_val" id="satuan_target"'); ?></td>
										</tr>
										<tr style="width:100%">
											<td>Target</td>
                                            <td><input style="width: 100%;" type="text" class="target" name="target[<?php echo $i; ?>]" value="<?php echo (!empty($row->target))?$row->target:''; ?>"></td>
                                            <td>Target</td>
                                            <td><input style="width: 100%;" type="text" class="target" name="target_thndpn[<?php echo $i; ?>]" value="<?php echo (!empty($row->target_thndpn))?$row->target_thndpn:''; ?>"></td>
										</tr>
									</table>
								</div>
							</div>
							<?php
									}
								}else{
							?>
							<div style="width: 100%; margin-top: 10px;">
								<div style="width: 100%;">
									<textarea class="common indikator_val" name="indikator_kinerja[1]" style="width:95%"></textarea>
								</div>
								<div style="width: 100%;">
									<table class="table-common" width="100%">
										<tr style="width:100%">
											<td>Satuan</td>
											<td colspan="3"><?php echo form_dropdown('satuan_target[1]', $satuan, '', 'class="common indikator_val" id="satuan_target"'); ?></td>
										</tr>
										<tr style="width:100%">
											<td>Target</td>
                                            <td><input style="width: 100%;" type="text" class="target" name="target[1]"></td>
                                            <td>Target</td>
                                            <td><input style="width: 100%;" type="text" class="target" name="target_thndpn[1]"></td>
										</tr>
									</table>
								</div>
							</div>
							<?php
								}
							?>
						</td>						
					</tr>					
				</tbody>
			</table>
		</form>
	</div>
	<footer>
		<div class="submit_link">    			
  			<input id="simpan" type="button" value="Simpan">
		</div> 
	</footer>
</div>
<div style="display: none" id="indikator_box_program">
	<div style="width: 100%; margin-top: 15px;">
		<hr>
		<div style="width: 100%;">
			<textarea class="common indikator_val" name="indikator_kinerja[]" style="width:95%"></textarea>
			<a class="icon-remove hapus_indikator_program" href="javascript:void(0)" style="vertical-align: top;"></a>
		</div>
		<div style="width: 100%;">
			<table class="table-common" width="100%">
				<tr style="width:100%">
					<td>Satuan</td>
					<td colspan="3"><?php echo form_dropdown('satuan_target[1]', $satuan, '', 'class="common indikator_val" id="satuan_target"'); ?></td>
                    
				</tr>
				<tr style="width:100%">
					<td>Target</td>
					<td><input style="width: 100%;" type="text" class="target" id="target" name="target[1]"></td>
                    <td>Target</td>
					<td><input style="width: 100%;" type="text" class="target" id="target_thndpn" name="target_thndpn[1]"></td>
				</tr>
			</table>
		</div>		
	</div>	
</div>