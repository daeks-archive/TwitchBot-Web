$(function() {

	$('.container').on('click', '[data-toggle="async"]', function(e) {
		e.preventDefault();
		var loadurl = $(this).attr('data-query');
		$.get(loadurl, function(data) {
			try {
				var obj = $.parseJSON(data);
				if (obj.status == 200) {
					if (obj.event.length > 0) {
						if (obj.data.length > 0) {
							toast('success', false, obj.data);
						}
						eval(obj.event);
					} else {
						var data = $('<textarea/>').html(obj.data).val();
						$(this).html(data);
					}
				} else if (obj.status == 500) {
					toast('danger', false, obj.data);
				} else {
					toast('danger', true, obj.data);
				}
			} catch (e) {
				infobox('danger', 0, e.message + data);
			}
		});
		return false;
	});
	
	$('.userbar').on('change', '[data-toggle="async"]', function(e) {
		e.preventDefault();
		var loadurl = $(this).attr('data-query');
		var selected = $(this).find("option:selected").val();
		$.get(loadurl + selected, function(data) {
			try {
				var obj = $.parseJSON(data);
				if (obj.status == 200) {
					if (obj.event.length > 0) {
						if (obj.data.length > 0) {
							toast('success', false, obj.data);
						}
						setTimeout(function(){
              eval(obj.event);
            }, 1000);
					} else {
            infobox('success', false, obj.data);
					}
				} else if (obj.status == 500) {
					toast('danger', false, obj.data);
				} else {
					toast('danger', true, obj.data);
				}
			} catch (e) {
				infobox('danger', 0, e.message + data);
			}
		});
		return false;
	});

});