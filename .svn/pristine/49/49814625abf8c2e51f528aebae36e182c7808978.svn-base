<table class="table-common" width="800px">
	<thead>
    	<tr>
        	<th align="center">No</th>
            <th align="center">Kode</th>
            <th align="center">Keterangan</th>
        </tr>
    </thead>
	<tbody>
    <?php
	$i=0;
	$temp = 1;
	$temp_checking = "";
	foreach ($revisi as $row) { 
	$temp_in = $row->kd_urusan.".".$row->kd_bidang.".".$row->kd_program.".".$row->kd_kegiatan." | ".$row->nama_prog_or_keg;
		if($temp_checking != $temp_in){
			$i++;
			$temp=1;
	?>
            <tr>
                <td><?php echo $i; ?></td >
                <td><?php echo $row->kd_urusan.".".$row->kd_bidang.".".$row->kd_program.".".$row->kd_kegiatan." | ".$row->nama_prog_or_keg;?></td>
                <td><?php echo $temp.". ".$row->ket;?></td>
            </tr>
    <?php 
			$temp_checking = $temp_in;
		} else {
			$temp++;
	?>
    		<tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td><?php echo $temp.". ".$row->ket;?></td>
            </tr>
	<?php
		}
	} ?>
	</tbody>
    <b><i>*Lakukan revisi sesuai dengan yang ada di kolom keterangan dengan nomor terakhir. <br />
    *Nomor-nomor sebelumnya adalah history dari revisi yang sudah dikerjakan.</i></b>
</table>