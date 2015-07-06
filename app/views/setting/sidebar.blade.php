<ul class="tm-nav uk-nav">
    <li class="uk-nav-header">Contents</li>
    <li @if ($pageaction == 'index') class="uk-active" @endif ><a href="/setting/index">一般</a></li>
    <li @if ($pageaction == 'review') class="uk-active" @endif ><a href="/setting/review">レビュー</a></li>
    <li @if ($pageaction == 'graph') class="uk-active" @endif ><a href="/setting/graph">グラフ</a></li>
    <li @if ($pageaction == 'surface') class="uk-active" @endif ><a href="/setting/surface">感性ワード</a></li>
</ul>
