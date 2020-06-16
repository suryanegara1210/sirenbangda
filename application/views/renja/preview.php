<table class="fcari" width="800px">
	<tbody>
		<tr>
			<td width="20%">SKPD</td>
			<td width="77%"><?php echo $renja->nama_skpd; ?></td>
		</tr>
		<tr>
			<td>Urusan</td>
			<td><?php echo $renja->kd_urusan.". ".$renja->Nm_Urusan; ?></td>
		</tr>		
		<tr>
			<td>Bidang Urusan</td>
			<td style="padding-left: 20px;"><?php echo $renja->kd_bidang.". ".$renja->Nm_Bidang; ?></td>
		</tr>
		<tr>
			<td>Program</td>
			<td style="padding-left: 43px;"><?php echo $renja->kd_program.". ".$renja->Ket_Program; ?></td>
		</tr>
		<tr>
			<td>Kegiatan</td>
			<td style="padding-left: 65px;"><?php echo $renja->kd_kegiatan.". ".$renja->nama_prog_or_keg; ?></td>
		</tr>
		<tr>
			<td>Indikator Kinerja</td>
			<td>
				<?php
					$ta=$this->m_settings->get_tahun_anggaran();
					$tahun_n1=0;
					$tahun_n1= $ta+1; 
					$i=0;
					foreach ($indikator_kegiatan as $row_kegiatan) {
						$i++;
						echo $i .". ". $row_kegiatan->indikator ."<BR>";
				?>
				<table class="table-common" width="100%">
					<tr>
						<th width="14%">Target <?php echo $ta?></th>
						<th width="14%">Target <?php echo $tahun_n1?></th>
					</tr>
					<tr>
						<td align="center"><?php echo $row_kegiatan->target." ".$row_kegiatan->nama_value; ?></td>
						<td align="center"><?php echo $row_kegiatan->target_thndpn." ".$row_kegiatan->nama_value; ?></td>
					</tr>
				</table>
				<hr>
				<?php
					}
				?>
		  </td>
		</tr>		
		<tr>
			<td>Penanggung Jawab</td>
			<td><?php echo $renja->penanggung_jawab; ?></td>
		</tr>
	</tbody>
</table>
<table class="table-common" width="100%">
	<tbody>			
		<tr>
			<th>Pagu Indikatif <?php echo $ta?></th>
			<th>Pagu Indikatif <?php echo $tahun_n1?></th>
		</tr>
		<tr>
			<td align="right">Rp. <?php echo Formatting::currency($renja->nominal); ?></td>
			<td align="right">Rp. <?php echo Formatting::currency($renja->nominal_thndpn); ?></td>
		</tr>
		<tr>
			<th>Lokasi <?php echo $ta?></th>
			<th>Lokasi <?php echo $tahun_n1?></th>
		</tr>
		<tr>
			<td align="left"><?php echo $renja->lokasi; ?></td>
			<td align="left"><?php echo $renja->lokasi_thndpn; ?></td>
		</tr>
		<tr>
			<th>Uraian Kegiatan <?php echo $ta?></th>
			<th>Uraian Kegiatan <?php echo $tahun_n1?></th>
		</tr>
		<tr>
			<td align="left"><?php echo $renja->catatan; ?></td>
			<td align="left"><?php echo $renja->catatan_thndpn; ?></td>
		</tr>
	</tbody>
</table>