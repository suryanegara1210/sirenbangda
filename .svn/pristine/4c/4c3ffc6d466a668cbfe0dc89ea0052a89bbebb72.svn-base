<article class="module width_full" style="width: 130%; margin-left: -15%;">
	<header>
	  <h3 style="width: auto;">
	  	Evaluasi Renja Pusat
	  </h3>
	</header>
  <div style="padding-top: 10px; padding-left: 22px;">
    <select data-placeholder="Pilih Triwulan" style="width: 500px;" class="common chosen-select" id="pilih_tw">
      <option></option>
    <?php
      for ($i=1; $i <= 4; $i++) {
    ?>
        <option value="<?= $i ?>">Triwulan <?= $i ?></option>
    <?php
      }
    ?>
    </select>
		<button style="margin: 5px 30px 5px 3px;" id="reload_table" title="Refresh Tabel Evaluasi Renja"><img src="<?= site_url('asset/images/detail2.png') ?>"> Search</button>
    <input style="margin: 0 5px 5px 3px; float:right;" link="<?= site_url('evaluasi_rkpd/export') ?>" type="button" id="export_btn" value="Export" />
  </div>
    <div id="table-rkpd" class="module_content" style="overflow:auto">
    </div>
</article>
<script type="text/javascript">
	$(document).ready(function(){
    prepare_chosen();

		$("#export_btn").click(function(){
			prepare_facebox();
			$.blockUI({
				css: window._css,
				overlayCSS: window._ovcss
			});
			var tw = $("#pilih_tw").val();
			window.open($(this).attr("link")+'/'+tw);
			$.unblockUI();
		});

    $("#reload_table").click(function(){
      if ($("#pilih_tw").val() == '') {
        alert('Triwulan belum dipilih ...');
        return false;
      }
      reload_table();
    });
	});

	function reload_table(){
		$.blockUI({
			css: window._css,
			overlayCSS: window._ovcss
		});

		$.ajax({
			type: "POST",
			url: '<?php echo site_url("evaluasi_rkpd/get_table_data"); ?>',
			dataType: "json",
			data: {tw: $("#pilih_tw").val()},
			success: function(msg){
				console.log(msg);
				catch_expired_session2(msg);
				$("div#table-rkpd").html(msg.html);
				$.unblockUI();
			}
		});
	}
</script>
