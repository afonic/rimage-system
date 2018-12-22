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
		});
	}
	var html = `
	<div class="btn-wrapper" id="toolbar-rimage-regen-all">
		<button id="rimage-regen-all" class="btn btn-small button-cancel">
			<span class="icon-loop" aria-hidden="true"></span>
			Renegerate all
		</button>
	</div>`;
	$("#toolbar").append(html);

	var items;
	$('#rimage-regen-all').click(function() {
        $('#rimage-regenerate').modal('toggle');
        var request = jQuery.ajax({
            method: "GET",
            url: '/administrator/index.php?rimage=regen',
            async: false
        }).responseText;

        var items = JSON.parse(request).data;
        $('.total-to-regen').text(items.length);

        $('#confirm-regenerate').click(function() {
        	$(this).attr("disabled", true);
        	var icon = $(this).find('span.icon-loop');
        	icon.addClass('icn-spinner');
			var token = $('#rimage-options').data('rtoken');
			var percentageEach = 100 / items.length;
			$.each(items, function(i, item) {
				jQuery.ajax({
					method: "POST",
					url: '/administrator/index.php',
					data: { rimage: 'regenitem', rid: item.id, rcatid: item.catid, rgallery: item.gallery, [token]: 1 },
					async: false
				})
				.done(function() {
					var percent = (i + 1) * percentageEach;
					$('#regen-progress').css({'width': percent+'%'});
					$('span.current').text(i + 1);
					if ((i + 1) == items.length) {
						icon.removeClass('icn-spinner');
						$('.modal-footer').notify("All images have been sucessfully regenerated.", {className: "success", elementPosition: "top center", autoHide: false});
					}
				})
				.error(function() {
					$('.modal-footer').notify("Images have NOT been sucessfully regenerated. Please contact admin.", {className: "error", elementPosition: "top center", autoHide: false});
				});
			})
        })
    });
});
