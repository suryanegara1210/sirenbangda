<?php		
	if (!empty($jendela_kontrol->baru) || !empty($jendela_kontrol->baru2) || !empty($jendela_kontrol->revisi) || !empty($jendela_kontrol->revisi2) || (empty($jendela_kontrol->baru) && empty($jendela_kontrol->baru2) && empty($jendela_kontrol->revisi) && empty($jendela_kontrol->revisi2) && empty($jendela_kontrol->kirim) && empty($jendela_kontrol->kirim2))) {
		$enable_add = TRUE;
	}else{
		$enable_add = FALSE;
	}
?>
<style type="text/css">
	.misi{
		margin: 5px;
	}

	.tujuan{
		margin-top: 2px;
		margin-bottom: 2px;
	}
	tr.tr-click:hover{		
		background-color: pink;
	}
	td.td-click{
		cursor: pointer;
	}

	#kegiatan-frame{
		margin-bottom: 25px;
	}

	#jendela_kontrol header, #jendela_kontrol footer, #jendela_kontrol h3{
		background: #337ab7;
		color: white;
	}

	#jendela_kontrol, #jendela_kontrol .module_content{
		background: #d9edf7;
	}
</style>
<script type="text/javascript">
	var element;
	$(document).ready(function(){
		$("td.td-click").click(function(){
			close_all();
			if($(this).parent().next().is(":visible")){
				$(this).parent().next().fadeOut();
				return false;
			};

			$("tr.tr-frame").hide();
			$.blockUI({
				css: window._css,
				overlayCSS: window._ovcss
			});

			element = $(this).parent();
			var idt = element.attr("id-t");
			var idr = element.attr("id-r");
			var this_element = $(this);
			$.ajax({
				type: "POST",
				url: '<?php echo site_url("renstra/get_sasaran_skpd"); ?>',
				data: {id_renstra: idr, id_tujuan: idt},
				success: function(msg){
					if (msg!="") {
						element.next().children().html(msg);
						element.next().fadeIn();
						element = this_element;						
						$.blockUI({
							timeout: 1000,
							css: window._css,
							overlayCSS: window._ovcss
						});
					};	
				}
			});
		});

		$(".tbh_sasaran").click(function(){
			close_all();
			element = $(this).parent().parent().find("td.td-click");			
			var idt = $(this).parent().parent().attr("id-t");
			var idr = $(this).parent().parent().attr("id-r");

			prepare_facebox();
			$.ajax({
				type: "POST",
				url: '<?php echo site_url("renstra/cru_sasaran_skpd"); ?>',
				data: {id_renstra: idr, id_tujuan: idt},
				success: function(msg){
					if (msg!="") {						
						$.facebox(msg);
					};	
				}
			});
		});

		$(document).on("click", ".close-program-frame", function(){
			close_program();			
		});		

		$(document).on("click", "#kirim_renstra", function(){
			prepare_facebox();
			$.facebox({div: '<?php echo site_url("renstra/kirim_renstra"); ?>'});
		});

		$(document).on("click", ".revisi", function(){			
			var idrevisi = $(this).attr("idR");
			var idr = $(this).attr("id-r");

			prepare_facebox();
			$.ajax({
				type: "POST",
				url: '<?php echo site_url("renstra/preview_revisi"); ?>',
				data: {id_renstra: idr, id_revisi: idrevisi},
				success: function(msg){
					if (msg!="") {						
						$.facebox(msg);
					};	
				}
			});
		});

		$(document).on("click", "#cetak", function(){
			$.blockUI({
				message: 'Cetak dokumen sedang di proses, mohon ditunggu hingga file terunduh secara otomatis ...',
				css: window._css,
				timeout: 2000,
				overlayCSS: window._ovcss
			});
			var link = '<?php echo site_url("renstra/do_cetak_renstra"); ?>';
			$(location).attr('href',link);
		});

		$(document).on("click", "#p_revisi", function(){
			prepare_facebox();
			$.blockUI({				
				css: window._css,				
				overlayCSS: window._ovcss
			});
			$.ajax({
				type: "POST",
				url: '<?php echo site_url("renstra/view_p_revisi"); ?>',
				dataType: "json",
				success: function(msg){
					catch_expired_session2(msg);
					if (msg.success==1) {						
						$.unblockUI();
						$.facebox(msg.msg);
					}else{
						$.blockUI({
							message: msg.msg,
							css: window._css,
							timeout: 2000,
							overlayCSS: window._ovcss
						});		
					}
				}
			});
		});		

		reload_jendela_kontrol();
	});

	function close_program(){
		$("#program-frame article").remove();
	}	

	function close_all(){		
		close_program();
	}

	function reload_jendela_kontrol(){
		$("#jendela_kontrol").load("<?php echo site_url('renstra/get_jendela_kontrol'); ?>");
	}
</script>

<article class="module width_full" id="jendela_kontrol"> 	
</article>

<article class="module width_full">
 	<header>
 		<h3>
			Renstra <?php echo $renstra->nama_skpd; ?>
		</h3>
 	</header>
 	<div class="module_content">	
		<table class="fcari" width="100%">
			<tbody>
				<tr>
					<td width="20%">SKPD</td>
					<td width="80%"><?php echo $renstra->nama_skpd; ?></td>
			</tr> 					
			<tr>
				<td>Visi</td>
				<td>
					<?php echo $renstra->visi; ?>
				</td>
			</tr>
			<tr>
				<td>Misi</td>
				<td>
				<?php							
					$i=0;
					foreach ($renstra_misi->result() as $row) {
						$i++;
				?>
					<div class="misi"><?php echo $i.". ".$row->misi; ?></div>
				<?php
					}	
				?>
				</td>
			</tr>					
			</tbody>
		</table>
		<table class="table-common" style="width: 99%">
			<thead>
				<tr>
					<th colspan="2">Tujuan</th>
					<th>Action</th>
				</tr>
			</thead>
			<tbody>
			<?php
				$i=0;
				foreach ($renstra_tujuan as $row) {
					$i++;
			?>
				<tr class="tr-click" id-r="<?php echo $row->id_renstra; ?>" id-t="<?php echo $row->id; ?>">
					<td class="td-click" width="50px"><?php echo $i; ?></td>
					<td class="td-click"><?php echo $row->tujuan; ?></td>
					<td align="center" width="50px">
					<?php
						if ($enable_add) {
					?>
						<a href="javascript:void(0)" class="icon-plus-sign tbh_sasaran" title="Tambah Sasaran"/>
					<?php
						}
					?>						
					</td>
				</tr>
				<tr class="tr-frame" style="display: none">
					<td colspan="3"></td>
				</tr>
			<?php
				}
			?>
			</tbody>
		</table>
 	</div> 	 	
</article>
<div id="program-frame">	
</div>