<?php
$presenter = new Illuminate\Pagination\UikitPresenter($paginator);
?>

<?php if ($paginator->getLastPage() > 1): ?>
    <ul class="uk-pagination uk-pagination-left">
        <?php echo $presenter->render(); ?>
    </ul>
<?php endif; ?>