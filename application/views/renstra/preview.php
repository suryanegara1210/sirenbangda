<table class="fcari" width="800px">
	<tbody>
		<tr>
			<td width="20%">SKPD</td>
			<td width="77%"><?php echo $renstra->nama_skpd; ?></td>
		</tr>		
		<tr>
			<td>Tujuan</td>
			<td><?php echo $renstra->tujuan; ?></td>
		</tr>
		<tr>
			<td>Sasaran</td>
			<td><?php echo $renstra->sasaran; ?></td>
		</tr>
		<tr>
			<td>Indikator Sasaran</td>
			<td>
				<?php  
					$i=0;
					foreach ($indikator_sasaran as $row1) {
						$i++;
						echo $i .". ". $row1->indikator ."<BR>";
					}
				?>
			</td>
		</tr>		
		<tr style="background-color: white;">			
			<td colspan="2"><hr></td>
		</tr>
		<tr>
			<td>Urusan</td>
			<td><?php echo $renstra->kd_urusan.". ".$renstra->Nm_Urusan; ?></td>
		</tr>		
		<tr>
			<td>Bidang Urusan</td>
			<td style="padding-left: 20px;"><?php echo $renstra->kd_bidang.". ".$renstra->Nm_Bidang; ?></td>
		</tr>
		<tr>
			<td>Program</td>
			<td style="padding-left: 43px;"><?php echo $renstra->kd_program.". ".$renstra->Ket_Program; ?></td>
		</tr>
		<tr>
			<td>Kegiatan</td>
			<td style="padding-left: 65px;"><?php echo $renstra->kd_kegiatan.". ".$renstra->nama_prog_or_keg; ?></td>
		</tr>
		<tr>
			<td>Indikator Kinerja</td>
			<td>
				<?php
					$i=0;
					foreach ($indikator_kegiatan as $row_kegiatan) {
						$i++;
						echo $i .". ". $row_kegiatan->indikator ."<BR>";
				?>
				<table class="table-common" width="100%">
					<tr>
						<th width="14%">Kondisi Awal</th>
						<th width="14%">Target 1</th>
						<th width="14%">Target 2</th>
						<th width="14%">Target 3</th>
						<th width="14%">Target 4</th>
						<th width="14%">Target 5</th>
						<th width="14%">Kondisi Akhir</th>
					</tr>
					<tr>
						<td align="center"><?php echo $row_kegiatan->kondisi_awal." ".$row_kegiatan->nama_value; ?></td>
						<td align="center"><?php echo $row_kegiatan->target_1." ".$row_kegiatan->nama_value; ?></td>
						<td align="center"><?php echo $row_kegiatan->target_2." ".$row_kegiatan->nama_value; ?></td>
						<td align="center"><?php echo $row_kegiatan->target_3." ".$row_kegiatan->nama_value; ?></td>
						<td align="center"><?php echo $row_kegiatan->target_4." ".$row_kegiatan->nama_value; ?></td>
						<td align="center"><?php echo $row_kegiatan->target_5." ".$row_kegiatan->nama_value; ?></td>
						<td align="center"><?php echo $row_kegiatan->target_kondisi_akhir." ".$row_kegiatan->nama_value; ?></td>
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
			<td><?php echo $renstra->penanggung_jawab; ?></td>
		</tr>
		<tr>
			<td>Lokasi</td>
			<td><?php echo $renstra->lokasi; ?></td>
		</tr>
		<tr style="background-color: white;">			
			<td colspan="2"><hr></td>
		</tr>
	</tbody>
</table>
<table class="fcari" width="800px">
	<tbody>			
		<tr>
			<th>Nominal 1</th>
			<th>Nominal 2</th>
			<th>Nominal 3</th>
			<th>Nominal 4</th>
			<th>Nominal 5</th>			
		</tr>
		<tr>
			<td align="right">Rp. <?php echo Formatting::currency($renstra->nominal_1); ?></td>
			<td align="right">Rp. <?php echo Formatting::currency($renstra->nominal_2); ?></td>
			<td align="right">Rp. <?php echo Formatting::currency($renstra->nominal_3); ?></td>
			<td align="right">Rp. <?php echo Formatting::currency($renstra->nominal_4); ?></td>
			<td align="right">Rp. <?php echo Formatting::currency($renstra->nominal_5); ?></td>
		</tr>
		<tr>
			<th>Lokasi 1</th>
			<th>Lokasi 2</th>
			<th>Lokasi 3</th>
			<th>Lokasi 4</th>
			<th>Lokasi 5</th>
		</tr>
		<tr>
			<td align="left"><?php echo $renstra->lokasi_1; ?></td>
			<td align="left"><?php echo $renstra->lokasi_2; ?></td>
			<td align="left"><?php echo $renstra->lokasi_3; ?></td>
			<td align="left"><?php echo $renstra->lokasi_4; ?></td>
			<td align="left"><?php echo $renstra->lokasi_5; ?></td>
		</tr>
		<tr>
			<th>Uraian Kegiatan 1</th>
			<th>Uraian Kegiatan 2</th>
			<th>Uraian Kegiatan 3</th>
			<th>Uraian Kegiatan 4</th>
			<th>Uraian Kegiatan 5</th>
		</tr>
		<tr>
			<td align="left"><?php echo $renstra->uraian_kegiatan_1; ?></td>
			<td align="left"><?php echo $renstra->uraian_kegiatan_2; ?></td>
			<td align="left"><?php echo $renstra->uraian_kegiatan_3; ?></td>
			<td align="left"><?php echo $renstra->uraian_kegiatan_4; ?></td>
			<td align="left"><?php echo $renstra->uraian_kegiatan_5; ?></td>
		</tr>
	</tbody>
</table>