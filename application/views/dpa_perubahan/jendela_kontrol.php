<style type="text/css">
	.r-ranwal{
		background-color: #D0BE2D;
		padding-left: 5px;
	}

	.r-akhir{
		background-color: #96CC3F;
		padding-left: 5px;
	}
</style>
<header>
	<h3>
	 
  <?php echo "Jendela Kontrol DPA ".$nama_skpd; ?></h3>
</header>
<div class="module_content"> 		
	<table class="fcari" width="100%">
		<tbody>
			<tr>
				<td align="center" colspan="6">Proses</td>					
			</tr>
			<tr align="center">					
				<td colspan="3" class="r-ranwal">
					Dokumen Pelaksanaan Anggaran</td>
			</tr>
			<tr>
				<td width="25%" class="r-ranwal">Program & Kegiatan Baru</td>
				<td colspan="2" width="25" class="r-ranwal"><?php echo $jendela_kontrol->baru; ?>
                Data</td>
			</tr>
			<tr>
			    <td class="r-ranwal">Program &amp; Kegiatan Diproses</td>
			    <td colspan="2" class="r-ranwal"><?php echo $jendela_kontrol->proses; ?>
			    Data</td>
		  </tr>
		</tbody>
	</table>
	<!--<table style="font-style: italic; color: #666;">
		<tr>
			<td colspan="2">*Keterangan:</td>				
		</tr>
		<tr>
			<td valign="top">1. </td>
			<td>bla bla</td>
		</tr>
		<tr>
			<td valign="top">2. </td>
			<td>bla bla</td>
		</tr>
		<tr>
			<td valign="top">2. </td>
			<td>bla bla.</td>
		</tr>		
	</table>-->		
</div> 	
<footer>
	<div class="submit_link">
    <?php if (!$dpa) {?>
    	<input type="button" class="button-action" id="get_rka" value="Ambil Data RKA Perubahan" />
    <?php }
		else {
	?>
    	<a href="<?php echo site_url('dpa_perubahan/preview_dpa'); ?>"><input type="button" value="Lihat DPA Perubahan" /></a>
    <?php } ?>
	 	<input type="button" value="Back" onclick="history.go(-1)" />
	</div> 
</footer>