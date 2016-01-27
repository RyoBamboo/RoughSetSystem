$(function() {

	/**
	 * 分析開始
	 */
	$("#startAnalysis").on('click', function() {
		console.log('分析開始');
		 var itemId = $(this).data('item-id');
		 var result = analysis(itemId, 'syntactic');
    });

	function analysis(itemId, method) {
		$.ajax({
			'type': 'POST',
			//'url': '/analysis/syntactic',
			//'url': '/analysis/synonym',
			'url': '/analysis/' + method,
			'data': { 'itemId': itemId }
		}).done(function(data) {
			console.log(data);
		}).fail(function(data) {
			console.log(data);
		});

	}
});