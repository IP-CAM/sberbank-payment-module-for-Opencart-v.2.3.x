<div class="buttons">
	<div class="pull-right">
		<input type="button" value="<?php echo $button_confirm; ?>" id="button-confirm" class="btn btn-primary" />
	</div>
</div>
<script type="text/javascript"><!--
$('#button-confirm').on('click', function() {
	$.ajax({
		type: 'get',
		url: 'index.php?route=extension/payment/sberbank/confirm',
		cache: false,
		dataType: "json",
		beforeSend: function() {
			$('#button-confirm').button('loading');
		},
		complete: function() {
			$('#button-confirm').button('reset');
		},
		success: function(json) {
			if (json.url == undefined) {
				alert(json.error);
			} else {
				document.location = json.url;
			}
		}
	});
});
//--></script>
