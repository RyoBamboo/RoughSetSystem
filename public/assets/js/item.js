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
			//'url': '/analysis/syntactic',
			'url': '/analysis/synonym',
			'data': data
		}).done(function(data) {
			console.log(data);
		}).fail(function(data) {
			console.log(data);
		});
    });

});