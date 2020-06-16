<script type="text/javascript">
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
						element_sasaran.trigger( "click" );
						reload_jendela_kontrol();
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
		var target_aw = "kondisi_awal["+ key +"]";
		var target_1 = "target_1["+ key +"]";
		var target_2 = "target_2["+ key +"]";
		var target_3 = "target_3["+ key +"]";
		var target_4 = "target_4["+ key +"]";
		var target_5 = "target_5["+ key +"]";
		var target_ah = "target_kondisi_akhir["+ key +"]";
		var satuan_target = "satuan_target["+ key +"]";

		$("#indikator_box_program textarea").attr("name", name);
		$("#indikator_box_program input#target_aw").attr("name", target_aw);
		$("#indikator_box_program input#target_1").attr("name", target_1);
		$("#indikator_box_program input#target_2").attr("name", target_2);
		$("#indikator_box_program input#target_3").attr("name", target_3);
		$("#indikator_box_program input#target_4").attr("name", target_4);
		$("#indikator_box_program input#target_5").attr("name", target_5);
		$("#indikator_box_program input#target_ah").attr("name", target_ah);
		$("#indikator_box_program select#satuan_target").attr("name", satuan_target);
		$("#indikator_frame_program").append($("#indikator_box_program").html());
	});

	$(document).on("click", ".hapus_indikator_program", function(){
		$(this).parent().parent().remove();
	});
</script>

<div style="width: 900px">
	<header>
		<h3>
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
		<form action="<?php echo site_url('renstra/save_program');?>" method="POST" name="program" id="program" accept-charset="UTF-8" enctype="multipart/form-data" >
			<input type="hidden" name="id_program" value="<?php if(!empty($program->id)){echo $program->id;} ?>" />
			<input type="hidden" name="id_renstra" value="<?php echo $id_renstra; ?>" />
			<input type="hidden" name="id_sasaran" value="<?php echo $id_sasaran; ?>" />
			<input type="hidden" id="nama_prog_or_keg" name="nama_prog_or_keg" value="<?php echo (!empty($program->nama_prog_or_keg))?$program->nama_prog_or_keg:''; ?>" />
			
			<table class="fcari" width="100%">
				<tbody>
					<tr>
						<td width="20%">SKPD</td>
						<td width="80%"><?php echo $skpd->nama_skpd; ?></td>
					</tr>
					<tr>
						<td>Tujuan</td>
						<td><?php echo $tujuan_n_sasaran->tujuan; ?></td>
					</tr>
					<tr>
						<td>Sasaran</td>
						<td><?php echo $tujuan_n_sasaran->sasaran; ?></td>
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
						<td>Indikator Kinerja <a id="tambah_indikator_program" class="icon-plus-sign" href="javascript:void(0)"></a></td>
						<td id="indikator_frame_program" key="<?php echo (!empty($indikator_program))?$indikator_program->num_rows():'1'; ?>">
							<?php
								if (!empty($indikator_program)) {
									$i=0;
									foreach ($indikator_program->result() as $row) {
										$i++;
							?>
							<input type="hidden" name="id_indikator_program[<?php echo $i; ?>]" value="<?php echo $row->id; ?>">
							<div style="width: 100%; margin-top: 10px;">
								<div style="width: 100%;">
									<textarea class="common indikator_val" name="indikator_kinerja[<?php echo $i; ?>]"><?php if(!empty($row->indikator)){echo $row->indikator;} ?></textarea>
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
										<tr>
											<td colspan="2">Satuan</td>
											<td colspan="5"><?php echo form_dropdown('satuan_target['. $i .']', $satuan, $row->satuan_target, 'class="common indikator_val" id="satuan_target"'); ?></td>
										</tr>
										<tr>
											<th>Kondisi Awal</th>
											<th>Target 1</th>
											<th>Target 2</th>
											<th>Target 3</th>
											<th>Target 4</th>
											<th>Target 5</th>
											<th>Kondisi Akhir</th>
										</tr>
										<tr>
											<td width="14%"><input style="width: 100%;" type="text" class="target" name="kondisi_awal[<?php echo $i; ?>]" value="<?php echo (!empty($row->kondisi_awal))?$row->kondisi_awal:''; ?>"></td>
											<td width="14%"><input style="width: 100%;" type="text" class="target" name="target_1[<?php echo $i; ?>]" value="<?php echo (!empty($row->target_1))?$row->target_1:''; ?>"></td>
											<td width="14%"><input style="width: 100%;" type="text" class="target" name="target_2[<?php echo $i; ?>]" value="<?php echo (!empty($row->target_2))?$row->target_2:''; ?>"></td>
											<td width="14%"><input style="width: 100%;" type="text" class="target" name="target_3[<?php echo $i; ?>]" value="<?php echo (!empty($row->target_3))?$row->target_3:''; ?>"></td>
											<td width="14%"><input style="width: 100%;" type="text" class="target" name="target_4[<?php echo $i; ?>]" value="<?php echo (!empty($row->target_4))?$row->target_4:''; ?>"></td>
											<td width="14%"><input style="width: 100%;" type="text" class="target" name="target_5[<?php echo $i; ?>]" value="<?php echo (!empty($row->target_5))?$row->target_5:''; ?>"></td>
											<td width="14%"><input style="width: 100%;" type="text" class="target" name="target_kondisi_akhir[<?php echo $i; ?>]" value="<?php echo (!empty($row->target_kondisi_akhir))?$row->target_kondisi_akhir:''; ?>"></td>
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
									<textarea class="common indikator_val" name="indikator_kinerja[1]"></textarea>
								</div>
								<div style="width: 100%;">
									<table class="table-common" width="100%">
										<tr>
											<td colspan="2">Satuan</td>
											<td colspan="5"><?php echo form_dropdown('satuan_target[1]', $satuan, '', 'class="common indikator_val" id="satuan_target"'); ?></td>
										</tr>
										<tr>
											<th>Kondisi Awal</th>
											<th>Target 1</th>
											<th>Target 2</th>
											<th>Target 3</th>
											<th>Target 4</th>
											<th>Target 5</th>
											<th>Kondisi Akhir</th>
										</tr>
										<tr>
											<td width="14%"><input style="width: 100%;" type="text" class="target" name="kondisi_awal[1]"></td>
											<td width="14%"><input style="width: 100%;" type="text" class="target" name="target_1[1]"></td>
											<td width="14%"><input style="width: 100%;" type="text" class="target" name="target_2[1]"></td>
											<td width="14%"><input style="width: 100%;" type="text" class="target" name="target_3[1]"></td>
											<td width="14%"><input style="width: 100%;" type="text" class="target" name="target_4[1]"></td>
											<td width="14%"><input style="width: 100%;" type="text" class="target" name="target_5[1]"></td>
											<td width="14%"><input style="width: 100%;" type="text" class="target" name="target_kondisi_akhir[1]"></td>
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
			<textarea class="common indikator_val" name="indikator_kinerja[]"></textarea>		
			<a class="icon-remove hapus_indikator_program" href="javascript:void(0)" style="vertical-align: top;"></a>
		</div>
		<div style="width: 100%;">
			<table class="table-common" width="100%">
				<tr>
					<td colspan="2">Satuan</td>
					<td colspan="5"><?php echo form_dropdown('satuan_target[1]', $satuan, '', 'class="common indikator_val" id="satuan_target"'); ?></td>
				</tr>
				<tr>
					<th>Kondisi Awal</th>
					<th>Target 1</th>
					<th>Target 2</th>
					<th>Target 3</th>
					<th>Target 4</th>
					<th>Target 5</th>
					<th>Kondisi Akhir</th>
				</tr>
				<tr>
					<td width="14%"><input style="width: 100%;" type="text" class="target" id="target_aw" name="kondisi_awal[1]"></td>
					<td width="14%"><input style="width: 100%;" type="text" class="target" id="target_1" name="target_1[1]"></td>
					<td width="14%"><input style="width: 100%;" type="text" class="target" id="target_2" name="target_2[1]"></td>
					<td width="14%"><input style="width: 100%;" type="text" class="target" id="target_3" name="target_3[1]"></td>
					<td width="14%"><input style="width: 100%;" type="text" class="target" id="target_4" name="target_4[1]"></td>
					<td width="14%"><input style="width: 100%;" type="text" class="target" id="target_5" name="target_5[1]"></td>
					<td width="14%"><input style="width: 100%;" type="text" class="target" id="target_ah" name="target_kondisi_akhir[1]"></td>
				</tr>
			</table>
		</div>		
	</div>	
</div>