<script type="text/javascript">
	function veri_renja(id){
		window.location = '<?php echo site_url("renja_perubahan/veri_readonly")?>/' + id;
	}
</script>
<article class="module width_full">
	<header>
	  <h3>Verifikasi Renja Untuk Rancangan Awal (Ranwal) Perubahan READONLY</h3>
	</header>
	<div class="module_content"; style="overflow:auto">
    Berikut merupakan daftar SKPD yang sudah mengirim renja perubahan untuk di verifikasi :
		<table id="renja" class="table-common" style="width:99%">
			<thead>
				<tr>
					<th width="10px">No</th>								
					<th>SKPD</th>					
					<th>Jumlah Kegiatan</th>					
					<th>Action</th>
				</tr>				
			</thead>
			<tbody>
		<?php
			$i=0;
			foreach ($renjas as $row) {		
				$i++;
		?>
				<tr>
					<td align="center"><?php echo $i; ?></td>
					<td><?php echo $row->nama_skpd; ?></td>
					<td align="center"><?php echo $row->jum_semua; ?></td>
					<td align="center"><a href="javascript:void(0)" onclick="veri_renja(<?php echo $row->id_skpd; ?>)" class="icon-edit" title="Lihat Verifikasi Renja"/></td>
				</tr>
		<?php
			}
		?>
			</tbody>
		</table>
	</div>
</article>