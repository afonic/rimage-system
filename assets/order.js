jQuery(document).ready(function($) {
	if ($('#rimage-order').length > 0) {
		var html = `
		<div class="btn-wrapper" id="toolbar-rimage-order">
			<button id="rimage-order-open" class="btn btn-small">
				<span class="icon-list-2" aria-hidden="true"></span>
				Order gallery
			</button>
		</div>`;
		$("#toolbar").append(html);

		$('#rimage-order-open').click(function() {
			$('#rimage-order').modal('toggle')
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
						data: { rimage: 'order', rid: $('#rimage-order').data('rid'), rdata: order}
					})
					.done(function() {
						sortable.option("disabled", false);
						$('.modal-footer').notify("Images have been sucessfully reordered.", {className: "success", elementPosition: "bottom center"});
					})
					.error(function() {
						sortable.option("disabled", true);
						$('.modal-footer').notify("Images have NOT been sucessfully reordered. Please contact admin.", {className: "error", elementPosition: "bottom center"});
					});
				}
			}
		});

		$.getJSON("/media/k2/galleries/"+$('#rimage-order').data('rid')+"/order.json", getOrder);
	    function getOrder(data) {	    	
	    	if (data) {
	    		sortable.sort(data);
	    	}
	    }
	}
});