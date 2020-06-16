<script type="text/javascript">	
	$(document).ready(function(){
		$('form#sasaran').validate({
			rules: {
				sasaran : "required",				
				satuan : "required",
				target_1 : {
							required:true,
							number:true
				},
				target_2 : {
							required:true,
							number:true
				},
				target_3 : {
							required:true,
							number:true
				},
				target_4 : {
							required:true,
							number:true
				},
				target_5 : {
							required:true,
							number:true
				},
				target_kondisi_akhir: {
							required:true,
							number:true
				},
				kondisi_awal: {
							required:true,
							number:true
				}				
			}			
		});

		$("#simpan").click(function(){
			$('#indikator_frame_sasaran .indikator_val').each(function () {
			    $(this).rules('add', {
			        required: true
			    });
			});

		    var valid = $("form#sasaran").valid();
		    if (valid) {
		    	element.parent().next().hide();
		    	$.blockUI({
					css: window._css,
					overlayCSS: window._ovcss
				});
		    	
		    	$.ajax({
					type: "POST",
					url: $("form#sasaran").attr("action"),
					data: $("form#sasaran").serialize(),
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
							element.trigger( "click" );
							reload_jendela_kontrol();
						};						
					}
				});
		    };
		});

		$("#tambah_indikator_sasaran").click(function(){
			key = $("#indikator_frame_sasaran").attr("key");
			key++;
			$("#indikator_frame_sasaran").attr("key", key);

			var name = "indikator["+ key +"]";
			$("#indikator_box_sasaran textarea").attr("name", name);		
			$("#indikator_frame_sasaran").append($("#indikator_box_sasaran").html());
		});

		$(document).on("click", ".hapus_indikator_sasaran", function(){
			$(this).parent().remove();
		});
	});
</script>
<div style="width: 900px">
	<header>
		<h3>
	<?php
		if (!empty($sasaran)){
		    echo "Edit Data Renstra";
		} else{
		    echo "Input Data Renstra";
		}
	?>
	</h3>
	</header>
	<div class="module_content">
		<form action="<?php echo site_url('renstra/save_sasaran');?>" method="POST" name="sasaran" id="sasaran" accept-charset="UTF-8" enctype="multipart/form-data" >
			<input type="hidden" name="id_sasaran" value="<?php if(!empty($sasaran->id)){echo $sasaran->id;} ?>" />
			<input type="hidden" name="id_renstra" value="<?php echo $id_renstra; ?>" />
			<input type="hidden" name="id_tujuan" value="<?php echo $tujuan->id; ?>" />
			<table class="fcari" width="100%">
				<tbody>
					<tr>
						<td width="20%">SKPD</td>
						<td width="80%"><?php echo $skpd->nama_skpd; ?></td>
					</tr>
					<tr>
						<td>Tujuan</td>
						<td><?php echo $tujuan->tujuan; ?></td>
					</tr>
					<tr>
						<td>Sasaran</td>
						<td>
							<textarea class="common" name="sasaran"><?php if(!empty($sasaran->sasaran)){echo $sasaran->sasaran;} ?></textarea>
						</td>
					</tr>
					<tr>
						<td>Indikator Kinerja <a id="tambah_indikator_sasaran" class="icon-plus-sign" href="javascript:void(0)"></a></td>
						<td id="indikator_frame_sasaran" key="<?php echo (!empty($indikator_sasaran))?$indikator_sasaran->num_rows():'1'; ?>">
							<?php
								if (!empty($indikator_sasaran)) {
									$i=0;
									foreach ($indikator_sasaran->result() as $row) {
										$i++;
							?>
							<input type="hidden" name="id_indikator_sasaran[<?php echo $i; ?>]" value="<?php echo $row->id; ?>">
							<div style="width: 100%; margin-top: 10px;">
								<textarea class="common indikator_val" name="indikator[<?php echo $i; ?>]"><?php if(!empty($row->indikator)){echo $row->indikator;} ?></textarea>
							<?php
								if ($i != 1) {
							?>
								<a class="icon-remove hapus_indikator_sasaran" href="javascript:void(0)" style="vertical-align: top;"></a>
							<?php
								}
							?>								
							</div>
							<?php
									}
								}else{
							?>
							<div style="width: 100%; margin-top: 10px;">
								<textarea class="common indikator_val" name="indikator[1]"></textarea>								
							</div>
							<?php
								}
							?>
						</td>
					</tr>
					<tr>
						<td colspan="2"><hr></td>
					</tr>
					<tr>
						<td>Satuan Target</td>
						<td><?php echo $satuan; ?></td>
					</tr>					
					<tr>
						<td>Kondisi Awal</td>
						<td><input class="common" name="kondisi_awal" value="<?php echo (!empty($sasaran->kondisi_awal))?$sasaran->kondisi_awal:''; ?>"></td>
					</tr>
					<tr>
						<td>Target Tahun 1</td>
						<td><input class="common" name="target_1" value="<?php echo (!empty($sasaran->target_1))?$sasaran->target_1:''; ?>"></td>
					</tr>
					<tr>
						<td>Target Tahun 2</td>
						<td><input class="common" name="target_2" value="<?php echo (!empty($sasaran->target_2))?$sasaran->target_2:''; ?>"></td>
					</tr>
					<tr>
						<td>Target Tahun 3</td>
						<td><input class="common" name="target_3" value="<?php echo (!empty($sasaran->target_3))?$sasaran->target_3:''; ?>"></td>
					</tr>
					<tr>
						<td>Target Tahun 4</td>
						<td><input class="common" name="target_4" value="<?php echo (!empty($sasaran->target_4))?$sasaran->target_4:''; ?>"></td>
					</tr>
					<tr>
						<td>Target Tahun 5</td>
						<td><input class="common" name="target_5" value="<?php echo (!empty($sasaran->target_5))?$sasaran->target_5:''; ?>"></td>
					</tr>
					<tr>
						<td>Target Kondisi Akhir</td>
						<td><input class="common" name="target_kondisi_akhir" value="<?php echo (!empty($sasaran->target_kondisi_akhir))?$sasaran->target_kondisi_akhir:''; ?>"></td>
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
<div style="display: none" id="indikator_box_sasaran">
	<div style="width: 100%; margin-top: 10px;">
		<textarea class="common indikator_val" name="indikator[]"></textarea>
		<a class="icon-remove hapus_indikator_sasaran" href="javascript:void(0)" style="vertical-align: top;"></a>
	</div>
</div>