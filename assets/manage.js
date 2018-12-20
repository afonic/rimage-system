jQuery(document).ready(function($) {
	if ($('#rimage-manage').length > 0) {
		var html = `
		<div class="btn-wrapper" id="toolbar-rimage-order">
			<button id="rimage-order-open" class="btn btn-small">
				<span class="icon-images" aria-hidden="true"></span>
				Manage gallery
			</button>
		</div>`;
		$("#toolbar").append(html);
		

		Dropzone.options.uploadImages =
		{
			url: '/administrator/index.php?rimage=upload&rid='+$('#rimage-manage').data('rid'),
			maxFilesize: 10,
			parallelUploads: 1,
			paramName: 'file',
			acceptedFiles: 'image/*'
		};

		$('#rimage-order-open').click(function() {
			$('#rimage-manage').modal('toggle');
		});

		var sortable = Sortable.create(rthumbs, { 
			animation: 300,
			draggable: ".rthumb",
				store: {
				set: function (sortable) {
					var order = sortable.toArray();
					sortable.option("disabled", true);
					jQuery.ajax({
						method: "POST",
						url: '/administrator/index.php',
						data: { rimage: 'order', rid: $('#rimage-manage').data('rid'), rdata: order}
					})
					.done(function() {
						sortable.option("disabled", false);
						$('.modal-footer').notify("Images have been sucessfully reordered.", {className: "success", elementPosition: "top center"});
					})
					.error(function() {
						sortable.option("disabled", false);
						$('.modal-footer').notify("Images have NOT been sucessfully reordered. Please contact admin.", {className: "error", elementPosition: "top center"});
					});
				}
			}
		});

		$.getJSON("/media/k2/galleries/"+$('#rimage-manage').data('rid')+"/order.json", getOrder);
	    function getOrder(data) {	    	
	    	if (data) {
	    		sortable.sort(data);
	    	}
	    }

	    $('#add-images').click(function() {
	    	$('.upload-images-container').addClass('open');
	    	$('.modal-body').css({'overflow-y': 'hidden'});
	    });


		$('#upload-images-cancel').click(function() {
			$('.upload-images-container').removeClass('open');
			$('.modal-body').css({'overflow-y': 'scroll'});
		});

	    $('.rthumb-delete').click(function() {
	    	var file = $(this).parent().data('id');
	    	var parent = $(this).parent();
	    	if (confirm("This cannot be undone, are you sure?")) {
		        jQuery.ajax({
					method: "POST",
					url: '/administrator/index.php',
					data: {rimage: 'delete', rid: $('#rimage-manage').data('rid'), rfile: file}
				})
				.done(function() {
					$('.modal-footer').notify("Image has been deleted. Please save the article to regen gallery.", {className: "warn", elementPosition: "top center"});
					parent.fadeOut().remove();
				})
				.error(function() {
					$('.modal-footer').notify("Image hasn't been deleted. Please contact admin.", {className: "error", elementPosition: "top center"});
				});
		    }
	    });
	}
});