<ul class="tm-nav uk-nav">
    <li class="uk-nav-header">Contents</li>
    <li @if ($pageaction == 'index') class="uk-active" @endif ><a href="/review">レビュー対象一覧</a></li>
    <li @if ($pageaction == 'add') class="uk-active" @endif ><a href="/review/add">新規レビュー対象登録</a></li>
    <li @if ($pageaction == 'review') class="uk-active" @endif ><a href="/setting/review">設定</a></li>
</ul>
