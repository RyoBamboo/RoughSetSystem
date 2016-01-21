$(function() {

	/**
	 * 分析開始
	 */
	$("#startAnalysis").on('click', function() {
		console.log('分析開始');
		var data = {
			'itemId': $(this).data('item-id')
		}
		$.ajax({
			'type': 'POST',
			'url': '/analysis/syntactic',
			'data': data
		}).done(function(data) {
			console.log(data);
		}).fail(function(xhr, textStatus, errorThrown) {
		});
    });

});