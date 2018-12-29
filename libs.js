$(document).on('click', '.favmod', function(e){
	e.preventDefault();
	var $this = $(this);
	ShowLoading();
	$.ajax({
		url: dle_root + 'engine/mods/favorites/ajax.php',
		type: 'POST',
		dataType: 'json',
		data: {newsid: $this.data('id')},
	})
	.done(function() {
		$this.toggleClass('active');
	})
	.fail(function(error) {
		DLEalert(error.responseText, dle_info);
	})
	.always(function() {
		HideLoading();
	});
})