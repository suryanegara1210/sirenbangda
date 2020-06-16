<thead style="font-size:8px">
	<tr>
		<th>No</th>
		<th>Sasaran</th>
		<th>Indikator Kinerja</th>
		<th>Kondisi Awal</th>
		<th>Tahun I</th>
		<th>Tahun II</th>
		<th>Tahun III</th>
		<th>Tahun IV</th>
		<th>Tahun V</th>
		<th>Tahun Akhir</th>
	</tr>
</thead>
<tbody style="font-size:8px">
<?php
	$i=0;
	foreach ($sasaran as $row) {
		$i++;
		$indikator = $this->m_renstra_trx->get_indikator_sasaran($row->id);
?>
	<tr>
		<td align="center"><?php echo $i; ?></td>
		<td><?php echo $row->sasaran; ?></td>
		<td>
<?php
		$i=0;
		foreach ($indikator as $row1) {
			$i++;
			echo $i .". ". $row1->indikator ."<BR>";
		}
?>
		</td>
		<td align="center"><?php echo $row->kondisi_awal." ".$row->nama_value; ?></td>
		<td align="center"><?php echo $row->target_1." ".$row->nama_value; ?></td>
		<td align="center"><?php echo $row->target_2." ".$row->nama_value; ?></td>
		<td align="center"><?php echo $row->target_3." ".$row->nama_value; ?></td>
		<td align="center"><?php echo $row->target_4." ".$row->nama_value; ?></td>
		<td align="center"><?php echo $row->target_5." ".$row->nama_value; ?></td>
		<td align="center"><?php echo $row->target_kondisi_akhir." ".$row->nama_value; ?></td>
	</tr>
<?php					
	}
?>		
</tbody>