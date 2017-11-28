$(function() {

  $('.search').keypress(function(e) {
    if(e.which == 13) {
        e.preventDefault();
    }
  });

	$('.search').typeahead({
		source: function(query, process) {
			$.ajax({
				url: this.$element.attr("data-query") + '/' + query,
				type: 'GET',
				dataType: this.$element.attr("data-type"),
				success: function(data) {
					$($('.search').attr("data-target")).html(data);
				}
			});
		}
	});
	
});