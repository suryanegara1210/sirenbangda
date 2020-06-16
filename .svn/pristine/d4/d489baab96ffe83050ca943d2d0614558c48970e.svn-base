<table class="fcari" width="800px">
	<tbody>
		<tr>
			<td width="20%">SKPD</td>
			<td width="77%"><?php echo $dpa->nama_skpd; ?></td>
		</tr>
		<tr>
			<td>Urusan</td>
			<td><?php echo $dpa->kd_urusan.". ".$dpa->Nm_Urusan; ?></td>
		</tr>		
		<tr>
			<td>Bidang Urusan</td>
			<td style="padding-left: 20px;"><?php echo $dpa->kd_bidang.". ".$dpa->Nm_Bidang; ?></td>
		</tr>
		<tr>
			<td>Program</td>
			<td style="padding-left: 43px;"><?php echo $dpa->kd_program.". ".$dpa->Ket_Program; ?></td>
		</tr>
		<tr>
			<td>Kegiatan</td>
			<td style="padding-left: 65px;"><?php echo $dpa->kd_kegiatan.". ".$dpa->nama_prog_or_keg; ?></td>
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
						<td align="center"><?php echo $row_kegiatan->target." ".$row_kegiatan->nama_value; ?></td>
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
			<td><?php echo $dpa->penanggung_jawab; ?></td>
		</tr>
	</tbody>
</table>
<table class="table-common" width="100%">
	<tbody>			
		<tr>
			<td align="center" width="25%">Triwulan I</td>
            <td align="center" width="25%" bgcolor="#CCCCCC">Triwulan II</td>
            <td align="center" width="25%">Triwulan III</td>
            <td align="center" width="25%" bgcolor="#CCCCCC">Triwulan IV</td>
		</tr>
        <tr>
            <td colspan="4" align="center">Pagu Indikatif</td>
        </tr>
		<tr>
			<td align="right">Rp. <?php echo Formatting::currency($dpa->nominal_1); ?></td>
			<td align="right" bgcolor="#CCCCCC">Rp. <?php echo Formatting::currency($dpa->nominal_2); ?></td>
            <td align="right">Rp. <?php echo Formatting::currency($dpa->nominal_3); ?></td>
            <td align="right" bgcolor="#CCCCCC">Rp. <?php echo Formatting::currency($dpa->nominal_4); ?></td>
		</tr>
        <tr>
            <td colspan="4" align="center">Ukuran Kinerja Triwulan</td>
        </tr>
		<tr>
			<td align="left"><?php echo $dpa->catatan_1; ?></td>
			<td align="left" bgcolor="#CCCCCC"><?php echo $dpa->catatan_2; ?></td>
            <td align="left"><?php echo $dpa->catatan_3; ?></td>
            <td align="left" bgcolor="#CCCCCC"><?php echo $dpa->catatan_4; ?></td>
		</tr>
        <tr>
            <td colspan="4" align="center">Keterangan</td>
        </tr>
		<tr>
			<td align="left"><?php echo $dpa->ket_1; ?></td>
			<td align="left" bgcolor="#CCCCCC"><?php echo $dpa->ket_2; ?></td>
            <td align="left"><?php echo $dpa->ket_3; ?></td>
            <td align="left" bgcolor="#CCCCCC"><?php echo $dpa->ket_4; ?></td>
		</tr>
	</tbody>
</table>