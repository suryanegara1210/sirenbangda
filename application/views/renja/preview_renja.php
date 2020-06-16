<article class="module width_full" style="width: 138%; margin-left: -19%;">
	<header>
	  <h3>Preview Renja</h3>
	</header>
	<div class="module_content";>
		<table class="table-display">
			<thead>
				<tr>
					<th colspan="12" align="center"><?php echo $renja_type; ?></th>
				</tr>
			</thead>
            <tbody>		
			<?php
				echo $renja;
			?>
            </tbody>
		</table>
	</div>		
	<footer>
		<div class="submit_link">
			<input type="button" value="Kembali" onclick="history.go(-1)">
		</div>
	</footer>
</article>