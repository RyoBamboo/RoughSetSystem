<script>
	$(function() {
		var modal = UIkit.modal(".tutorial-modal");
		modal.show();
	})
</script>

<!-- This is the modal -->
<div  class="uk-modal tutorial-modal">
    <div class="uk-modal-dialog uk-modal-dialog-large">
        <a class="uk-modal-close uk-close"></a>
        <div class="uk-slidenav-position" data-uk-slideshow>
	    <ul class="uk-slideshow">
		<li><img src="/assets/img/test.png" width="" height="" alt=""></li>
		<li><img src="/assets/img/test3.png" width="" height="" alt=""></li>
		<li><img src="/assets/img/test4.png" width="" height="" alt=""></li>
		<li><img src="/assets/img/test5.png" width="" height="" alt=""></li>
	    </ul>
	<a href="" class="uk-slidenav uk-slidenav-contrast uk-slidenav-previous" data-uk-slideshow-item="previous">前へ</a>
	<a href="" class="uk-slidenav uk-slidenav-contrast uk-slidenav-next" data-uk-slideshow-item="next">次へ</a>
	</div>
    </div>
</div>

<a href='/graph'>次のステップへ進む</a>
