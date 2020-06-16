<script type="text/javascript">	
	$(document).ready(function(){
		$('form#sasaran').validate({
			rules: {
				sasaran : "required",
				strategi : "required",				
			}			
		});

		$("#simpan").click(function(){			
			$('#kebijakan_frame .kebijakan_val').each(function () {
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
						};						
					}
				});
		    };
		});

		$("#tambah_kebijakan").click(function(){
			key = $("#kebijakan_frame").attr("key");
			key++;
			$("#kebijakan_frame").attr("key", key);

			var name = "kebijakan["+ key +"]";
			$("#kebijakan_box textarea").attr("name", name);		
			$("#kebijakan_frame").append($("#kebijakan_box").html());
		});

		$(document).on("click", ".hapus_kebijakan", function(){
			$(this).parent().remove();
		});
	});
</script>
<div style="width: 900px">
	<header>
		<h3>
	<?php
		if (!empty($sasaran)){
		    echo "Edit Data RPJMD";
		} else{
		    echo "Input Data RPJMD";
		}
	?>
	</h3>
	</header>
	<div class="module_content">
		<form action="<?php echo site_url('rpjmd/save_sasaran');?>" method="POST" name="sasaran" id="sasaran" accept-charset="UTF-8" enctype="multipart/form-data" >
			<input type="hidden" name="id_sasaran" value="<?php if(!empty($sasaran->id)){echo $sasaran->id;} ?>" />
			<input type="hidden" name="id_rpjmd" value="<?php echo $id_rpjmd; ?>" />
			<input type="hidden" name="id_tujuan" value="<?php echo $tujuan->id; ?>" />
			<table class="fcari" width="100%">
				<tbody>					
					<tr>
						<td width="20%">Tujuan</td>
						<td width="80%"><?php echo $tujuan->tujuan; ?></td>
					</tr>
					<tr>
						<td>Sasaran</td>
						<td>
							<textarea class="common" name="sasaran"><?php if(!empty($sasaran->sasaran)){echo $sasaran->sasaran;} ?></textarea>
						</td>
					</tr>
					<tr>
						<td>Strategi</td>
						<td>
							<textarea class="common" name="strategi"><?php if(!empty($sasaran->strategi)){echo $sasaran->strategi;} ?></textarea>
						</td>
					</tr>
					<tr>
						<td>Arah Kebijakan <a id="tambah_kebijakan" class="icon-plus-sign" href="javascript:void(0)"></a></td>
						<td id="kebijakan_frame" key="<?php echo (!empty($kebijakan_sasaran))?$kebijakan_sasaran->num_rows():'1'; ?>">
							<?php
								if (!empty($kebijakan_sasaran)) {
									$i=0;
									foreach ($kebijakan_sasaran->result() as $row) {
										$i++;
							?>
							<input type="hidden" name="id_kebijakan[<?php echo $i; ?>]" value="<?php echo $row->id; ?>">
							<div style="width: 100%; margin-top: 10px;">
								<textarea class="common kebijakan_val" name="kebijakan[<?php echo $i; ?>]"><?php if(!empty($row->kebijakan)){echo $row->kebijakan;} ?></textarea>
							<?php
								if ($i != 1) {
							?>
								<a class="icon-remove hapus_kebijakan" href="javascript:void(0)" style="vertical-align: top;"></a>
							<?php
								}
							?>								
							</div>
							<?php
									}
								}else{
							?>
							<div style="width: 100%; margin-top: 10px;">
								<textarea class="common kebijakan_val" name="kebijakan[1]"></textarea>								
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
<div style="display: none" id="kebijakan_box">
	<div style="width: 100%; margin-top: 10px;">
		<textarea class="common kebijakan_val" name="kebijakan[]"></textarea>
		<a class="icon-remove hapus_kebijakan" href="javascript:void(0)" style="vertical-align: top;"></a>
	</div>
</div>