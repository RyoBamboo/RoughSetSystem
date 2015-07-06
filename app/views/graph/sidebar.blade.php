<ul class="tm-nav uk-nav">
    <li class="uk-nav-header">Contents</li>
    <li @if ($pageaction == 'index') class="uk-active" @endif ><a href="/graph">単体グラフ</a></li>
    <li @if ($pageaction == 'diff') class="uk-active" @endif ><a href="/graph/diff">差分グラフ</a></li>
    <li @if ($pageaction == 'graph') class="uk-active" @endif ><a href="/setting/graph">設定</a></li>
</ul>
