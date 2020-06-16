<?php
	$tahun_sekarang=$this->session->userdata('t_anggaran_aktif');
	$max_col_keg=1;
?>
			<thead>
				<tr>
					<th rowspan="3" colspan="4">Kode</th>
					<th rowspan="3" >Program dan Kegiatan</th>
					<th rowspan="3" >Kriteria Keberhasilan</th>
					<th rowspan="3" >Ukuran Keberhasilan</th>
					<th colspan="4" >Kinerja Triwulan</th>
				</tr>
				<tr>
					<th>Triwulan I (Rp.)</th>
                    <th>Triwulan II (Rp.)</th>
                    <th>Triwulan III (Rp.)</th>
                    <th>Triwulan IV (Rp.)</th>
				</tr>
			</thead>
			<tbody>
				<?php
					foreach ($urusan as $row_urusan) {
					echo "
					<tr bgcolor=\"#78cbfd\">
						<td>".$row_urusan->kd_urusan."</td>
						<td></td>
						<td></td>
						<td></td>
						<td colspan=\"7 \">
							<strong>".strtoupper($row_urusan->nama_urusan)."</strong>
						</td>
					
					</tr>";
					
					$bidang = $this->db->query("
						SELECT t.*,b.Nm_Bidang as nama_bidang from (
						SELECT
							pro.kd_urusan,pro.kd_bidang,pro.kd_program,pro.kd_kegiatan,
							SUM(keg.nominal_1) AS sum_nominal_1,
							SUM(keg.nominal_2) AS sum_nominal_2,
							SUM(keg.nominal_3) AS sum_nominal_3,
							SUM(keg.nominal_4) AS sum_nominal_4
						FROM
							(SELECT * FROM tx_dpa_prog_keg_perubahan WHERE is_prog_or_keg=1) AS pro
						INNER JOIN
							(SELECT * FROM tx_dpa_prog_keg_perubahan WHERE is_prog_or_keg=2) AS keg ON keg.parent=pro.id
						WHERE
							keg.id_skpd=".$id_skpd."
							AND keg.tahun=".$ta."
						AND keg.kd_urusan = ".$row_urusan->kd_urusan."
						GROUP BY pro.kd_urusan,kd_bidang
						ORDER BY kd_urusan ASC, kd_bidang ASC, kd_program ASC
						) t
						left join m_bidang b
						on t.kd_urusan = b.Kd_Urusan and t.kd_bidang = b.Kd_Bidang
					")->result();
					
					foreach ($bidang as $row_bidang) {
						echo "
						<tr bgcolor=\"#00FF33\">
							<td>".$row_urusan->kd_urusan."</td>
							<td>".$row_bidang->kd_bidang."</td>
							<td></td>
							<td></td>
							<td colspan=\"7 \">
								<strong>".strtoupper($row_bidang->nama_bidang)."</strong>
							</td>
						</tr>";
					
					$program = $this->m_dpa_perubahan->get_program_skpd_4_cetak($id_skpd,$ta,$row_urusan->kd_urusan,$row_bidang->kd_bidang);
					
					foreach ($program as $row) {
						$result = $this->m_dpa_perubahan->get_kegiatan_skpd_4_cetak($row->id,$tahun_sekarang);
						$kegiatan = $result->result();
						$indikator_program = $this->m_dpa_perubahan->get_indikator_prog_keg_perubahan($row->id, FALSE, TRUE);
						$temp = $indikator_program->result();
						$total_temp = $indikator_program->num_rows();
						
						$col_indikator=1;
						$go_2_keg = FALSE;
						$total_for_iteration = $total_temp;
						if($total_temp > $max_col_keg){
							$total_temp = $max_col_keg;
							$go_2_keg = TRUE;
						}
				?>
				<tr bgcolor="#FF9933">
					<td style="border-bottom: 0;" rowspan="<?php echo $total_temp; ?>"><?php echo $row->kd_urusan; ?></td>
					<td style="border-bottom: 0;" rowspan="<?php echo $total_temp; ?>"><?php echo $row->kd_bidang; ?></td>
					<td style="border-bottom: 0;" rowspan="<?php echo $total_temp; ?>"><?php echo $row->kd_program; ?></td>
					<td style="border-bottom: 0;" rowspan="<?php echo $total_temp; ?>"><?php echo $row->kd_kegiatan; ?></td>
					<td style="border-bottom: 0;" rowspan="<?php echo $total_temp; ?>"><?php echo $row->nama_prog_or_keg; ?></td>
					<td>
						<?php
                            echo $temp[0]->indikator;
                        ?>
                    </td>
                    <td>
                        <?php
                            echo $temp[0]->indikator." ".$temp[0]->target." ".$temp[0]->satuan_target;
                        ?>
                    </td>
					<td align="right" style="border-bottom: 0;" rowspan="<?php echo $total_temp; ?>">
						<?php echo Formatting::currency($row->sum_nominal_1); ?>
                    </td>
					<td align="right" style="border-bottom: 0;" rowspan="<?php echo $total_temp; ?>">
                    	<?php echo Formatting::currency($row->sum_nominal_2); ?>
                    </td>
					<td align="right" style="border-bottom: 0;" rowspan="<?php echo $total_temp; ?>">
                    	<?php echo Formatting::currency($row->sum_nominal_3); ?>
                    </td>
					<td align="right" style="border-bottom: 0;" rowspan="<?php echo $total_temp; ?>">
                    	<?php echo Formatting::currency($row->sum_nominal_4); ?>
                    </td>
				</tr>
				<?php
					if ($total_for_iteration > 1) {			
						for ($i=1; $i < $total_for_iteration; $i++) { 
							$col_indikator++;
				?>
					<tr>
						<?php
								if ($go_2_keg && $col_indikator > $max_col_keg) { 
							?>
								<td style="border-top: 0;border-bottom: 0;" ></td>
								<td style="border-top: 0;border-bottom: 0;" ></td>
								<td style="border-top: 0;border-bottom: 0;" ></td>
								<td style="border-top: 0;border-bottom: 0;" ></td>
								<td style="border-top: 0;border-bottom: 0;" ></td>
							<?php
								}
							?>
							<td>
								<?php
									echo $temp[$i]->indikator;
								?>
							</td>
							<td>
								<?php
									echo $temp[$i]->indikator." ".$temp[$i]->target." ".$temp[$i]->satuan_target;
								?>
							</td>
							<?php
								if ($go_2_keg && $col_indikator > $max_col_keg) {
							?>
								<td style="border-top: 0;border-bottom: 0;" ></td>
								<td style="border-top: 0;border-bottom: 0;" ></td>
								<td style="border-top: 0;border-bottom: 0;" ></td>
								<td style="border-top: 0;border-bottom: 0;" ></td>
					<?php
							}
					?>
						</tr>
					<?php
						}
					}
					foreach ($kegiatan as $row) {						
							$indikator_kegiatan = $this->m_dpa_perubahan->get_indikator_prog_keg_perubahan($row->id, FALSE, TRUE);
							$temp = $indikator_kegiatan->result();			
							$total_temp = $indikator_kegiatan->num_rows();

							$go_2_keg = FALSE;
							$col_indikator_keg=1;
							$total_for_iteration = $total_temp;
							if ($total_temp > $max_col_keg) {
								$total_temp = $max_col_keg;
								$go_2_keg = TRUE;
							}
					?>
					<tr>
						<td style="border-bottom: 0;" rowspan="<?php echo $total_temp; ?>"><?php echo $row->kd_urusan; ?></td>
						<td style="border-bottom: 0;" rowspan="<?php echo $total_temp; ?>"><?php echo $row->kd_bidang; ?></td>
						<td style="border-bottom: 0;" rowspan="<?php echo $total_temp; ?>"><?php echo $row->kd_program; ?></td>
						<td style="border-bottom: 0;" rowspan="<?php echo $total_temp; ?>"><?php echo $row->kd_kegiatan; ?></td>
						<td style="border-bottom: 0;" rowspan="<?php echo $total_temp; ?>"><?php echo $row->nama_prog_or_keg; ?></td>
						<td>
							<?php
								echo $temp[0]->indikator;
							?>
						</td>
						<td>
							<?php
								echo $temp[0]->indikator." ".$temp[0]->target." ".$temp[0]->satuan_target;
							?>
						</td>
						<td align="right" style="border-bottom: 0;" rowspan="<?php echo $total_temp; ?>">
                    	<?php echo Formatting::currency($row->nominal_1); ?>
                        </td>
                        <td align="right" style="border-bottom: 0;" rowspan="<?php echo $total_temp; ?>">
                        <?php echo Formatting::currency($row->nominal_2); ?>
                        </td>
                        <td align="right" style="border-bottom: 0;" rowspan="<?php echo $total_temp; ?>">
                        <?php echo Formatting::currency($row->nominal_3); ?>
                        </td>
                        <td align="right" style="border-bottom: 0;" rowspan="<?php echo $total_temp; ?>">
                        <?php echo Formatting::currency($row->nominal_4); ?>
                    	</td>
					</tr>
					<?php
						if ($total_for_iteration > 1) {
						for ($i=1; $i < $total_for_iteration; $i++) { 					
							$col_indikator_keg++;
					?>
						<tr>
						<?php
							if ($go_2_keg && $col_indikator_keg > $max_col_keg) {
						?>
							<td style="border-top: 0;border-bottom: 0;" ></td>
							<td style="border-top: 0;border-bottom: 0;" ></td>
							<td style="border-top: 0;border-bottom: 0;" ></td>
							<td style="border-top: 0;border-bottom: 0;" ></td>
							<td style="border-top: 0;border-bottom: 0;" ></td>
						<?php
							}
						?>
							<td>
								<?php
									echo $temp[$i]->indikator;
								?>
							</td>
							<td>
								<?php
									echo $temp[$i]->indikator." ".$temp[$i]->target." ".$temp[$i]->satuan_target;
								?>
							</td>
							<td style="border-top: 0;border-bottom: 0;" ></td>
							<td style="border-top: 0;border-bottom: 0;" ></td>
							<td style="border-top: 0;border-bottom: 0;" ></td>
							<td style="border-top: 0;border-bottom: 0;" ></td>
					</tr>
					<?php
						}
					}
				}
			}
					}
					}
					?>
			</tbody>