jQuery(document).ready(function($) {
	if ($('#rimage-options').length > 0) {
		var html = `
		<div class="btn-wrapper" id="toolbar-rimage-regen">
			<button id="rimage-regen" class="btn btn-small button-cancel">
				<span class="icon-refresh" aria-hidden="true"></span>
				Renegerate images
			</button>
		</div>`;
		$("#toolbar").append(html);

		$('#rimage-regen').click(function() {
			$(this).attr("disabled", true);
			var r = $('#rimage-options');
			var token = r.data('rtoken');
			jQuery.ajax({
				method: "POST",
				url: '/administrator/index.php',
				data: { rimage: 'regenitem', rid: r.data('id'), rcatid: r.data('category'), rgallery: r.data('gallery'), [token]: 1 }
			})
			.done(function() {
				$('#toolbar').notify("Images have been sucessfully regenerated.", {className: "success", elementPosition: "bottom center"});
				$('#rimage-regen').attr("disabled", false);
			})
			.error(function() {
				$('#toolbar').notify("Images have NOT been sucessfully regenerated. Please contact admin.", {className: "error", elementPosition: "bottom center"});
			});
		})
	}
});
